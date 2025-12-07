<?php
session_start();

header('Content-Type: application/json');

$user_id = $_SESSION['user']['id'] ?? $_SESSION['userId'] ?? null;
$isLoggedIn = isset($_SESSION['user']) || isset($_SESSION['logged_in']);

if (!$isLoggedIn) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/model/database.php';
require_once __DIR__ . '/model/BillingService.php';
require_once __DIR__ . '/model/users_db.php';
require_once __DIR__ . '/model/email_server.php';
require_once __DIR__ . '/model/companies_db.php';

$usersDb = new UsersDB();
$companies_db = new CompaniesDB();

$stripeCustomerId = trim($_POST['stripe_customer_id'] ?? $_GET['stripe_customer_id'] ?? '');
$customerId = trim($_POST['customer_id'] ?? $_GET['customer_id'] ?? '');
$stripeCustomerRowId = null;
$inviteeEmail = null;
$inviteeName = null;

// Get some user information to pass below
try {
    $user_info = $usersDb->get_user_info($user_id);
    $user_full_name = $user_info['firstname'] . ' ' . $user_info['lastname'];
    $company_info = $companies_db->get_company_from_customer($customerId);
    $company_name = $company_info['Name'] ?? null;
    if (empty($company_name)) {
        throw new Exception('Company name not found for customer ' . $customerId);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Could Not Locate Critical Info: ' . $e->getMessage()]);
}

try {
    if (empty($stripeCustomerId) && !empty($customerId)) {
        $db = Database::getDB();

        // 1) Map CRM CustomerId -> owning userId
        $customerLookup = $db->prepare('SELECT userId FROM Customer WHERE CustomerId = :customer_id LIMIT 1');
        $customerLookup->bindValue(':customer_id', $customerId);
        $customerLookup->execute();
        $custRow = $customerLookup->fetch();
        $customerLookup->closeCursor();
        if (!$custRow || !isset($custRow['userId'])) {
            throw new Exception('No owner user found for that customer.');
        }
        $ownerUserId = $custRow['userId'];

        // 2) Gather owner user info for Stripe customer creation if needed
        $userInfo = $usersDb->get_user_info($ownerUserId);
        if (!$userInfo) {
            throw new Exception('No user record found for owner userId ' . $ownerUserId);
        }
        $email = $userInfo['email'] ?? '';
        $name = trim(($userInfo['firstname'] ?? '') . ' ' . ($userInfo['lastname'] ?? ''));
        if (empty($email)) {
            throw new Exception('Owner user has no email; cannot create Stripe customer.');
        }
        $inviteeEmail = $email;
        $inviteeName = $name;

        // 3) Ensure Stripe customer exists (creates if missing) and capture both external id and local row id
        $stripeCustomerRow = BillingService::ensureStripeCustomerRow($customerId, $email, $name);
        $stripeCustomerId = $stripeCustomerRow['stripe_customer_id'] ?? null;
        $stripeCustomerRowId = $stripeCustomerRow['StripeCustomerId'] ?? $stripeCustomerRow['Id'] ?? null;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Lookup failed: ' . $e->getMessage()]);
    exit;
}

if (empty($stripeCustomerId) || empty($stripeCustomerRowId)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing stripe_customer_id or unmapped customer']);
    exit;
}

try {
    $inviteCode = BillingService::createPaymentMethodInviteForCustomer((int) $stripeCustomerRowId);

    // Send invite email with internal account
    $email_server = new EmailServer();
    $email_account_type = 'internal';
    $email_password = $email_server->get_email_password($email_account_type);
    $invite_link = 'https://corsairetech.com/billing_add_bank_view.php';
    if ($inviteeEmail) {
        $email_server->send_payment_invite($inviteeEmail, $inviteCode, $invite_link, $email_password, $user_full_name, $company_name);
    }

    echo json_encode([
        'success' => true,
        'inviteCode' => $inviteCode,
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Could not create invite: ' . $e->getMessage()]);
}
