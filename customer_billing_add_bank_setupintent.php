<?php
session_start();

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['PaymentMethodInviteId'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Invite not verified or expired']);
        exit;
    }

    require_once __DIR__ . '/vendor/autoload.php';
    require_once __DIR__ . '/model/database.php';
    require_once __DIR__ . '/model/BillingService.php';

    $inviteId = (int) $_SESSION['PaymentMethodInviteId'];
    $invite = BillingService::getActiveInviteById($inviteId);

    if (!$invite) {
        http_response_code(401);
        echo json_encode(['error' => 'Invite not verified or expired']);
        exit;
    }

    $stripeCustomerId = BillingService::getStripeCustomerIdForInvite($invite);

    if (!$stripeCustomerId) {
        http_response_code(400);
        echo json_encode(['error' => 'Unable to resolve Stripe customer for invite']);
        exit;
    }

    $setupIntent = BillingService::createBankAccountSetupIntentForStripeCustomer($stripeCustomerId);

    echo json_encode([
        'clientSecret' => $setupIntent->client_secret,
        'stripeCustomerId' => $setupIntent->customer,
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
