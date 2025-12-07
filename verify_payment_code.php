<?php
session_start();

header('Content-Type: application/json');

$code = trim($_POST['code'] ?? '');

if (empty($code)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Code is required']);
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/model/database.php';
require_once __DIR__ . '/model/BillingService.php';

$invite = BillingService::verifyInviteCode($code);

if (!$invite) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid or expired code']);
    exit;
}

$_SESSION['PaymentMethodInviteId'] = $invite['PaymentMethodInviteId'];
$_SESSION['InviteStripeCustomerId'] = $invite['StripeCustomerId'];

echo json_encode(['success' => true]);
