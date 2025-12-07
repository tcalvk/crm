<?php

session_start();

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/model/database.php';
require_once __DIR__ . '/model/BillingService.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$user = $_SESSION['user'];
$userId = $user['id'];
$email = $user['email'];
$name = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));

try {
    $setupIntent = BillingService::createBankAccountSetupIntent($userId, $email, $name);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}

echo json_encode([
    'clientSecret' => $setupIntent->client_secret,
    'stripeCustomerId' => $setupIntent->customer,
]);
