<?php

header('Content-Type: text/plain; charset=utf-8');

// TEMP check for dev
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo 'Stripe webhook endpoint is alive (dev)';
    exit;
}

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../model/database.php';
require_once __DIR__ . '/../model/log_statements_db.php';
require_once __DIR__ . '/../model/BillingService.php';

use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\PaymentMethod;
use PDO;

$config = require __DIR__ . '/../config/stripe_dev.php';

$stripeSecret = $config['stripe_secret_key'];
$webhookSecret = $config['webhook_secret'];

\Stripe\Stripe::setApiKey($stripeSecret);
$log_statements_db = new LogStatementsDB();

/**
 * Store the Stripe event payload for auditing.
 */
function record_stripe_event($event): void
{
    $eventJson = json_encode($event->toArray(), JSON_UNESCAPED_UNICODE);
    try {
        BillingService::create_stripe_event($eventJson);
    } catch (\Throwable $e) {
        error_log('Stripe webhook: failed to record event: ' . $e->getMessage());
    }
}

// Get the raw body
$payload = file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

try {
    $event = \Stripe\Webhook::constructEvent(
        $payload,
        $sig_header,
        $webhookSecret
    );
} catch(Exception $e) {
    http_response_code(400);
    echo 'Invalid signature: ' . $e->getMessage();
    exit;
}

switch ($event->type) {
    case 'payment_intent.succeeded':
        $paymentIntent = $event->data->object; // \Stripe\PaymentIntent

        BillingService::update_stripe_payment_status($paymentIntent->id, 'succeeded');
        record_stripe_event($event);

        $statement = BillingService::get_statement_for_payment_intent($paymentIntent->id);
        if ($statement && !empty($statement['StatementNumber'])) {
            $paid_date = date('Y-m-d');

            $amountCents = $paymentIntent->amount_received ?? $paymentIntent->amount ?? $statement['AmountCents'] ?? null;
            if ($amountCents !== null) {
                $payment_amount = ((float)$amountCents) / 100;
                $log_statements_db->mark_as_paid((int)$statement['StatementNumber'], $paid_date, $payment_amount);
            } else {
                error_log("Unable to determine payment amount for payment_intent {$paymentIntent->id}");
            }
        } else {
            error_log("No statement found for payment_intent {$paymentIntent->id}");
        }

        http_response_code(200);
        exit();
        break;

    case 'payment_intent.payment_failed':
        $paymentIntent = $event->data->object; // \Stripe\PaymentIntent

        BillingService::update_stripe_payment_status($paymentIntent->id, 'failed');
        record_stripe_event($event);

        http_response_code(200);
        exit();
        break;

    case 'charge.dispute.created':
        $dispute = $event->data->object; // \Stripe\Dispute
        $paymentIntentId = $dispute->payment_intent ?? null;

        if ($paymentIntentId) {
            BillingService::update_stripe_payment_status($paymentIntentId, 'disputed');
            record_stripe_event($event);

            $statement = BillingService::get_statement_for_payment_intent($paymentIntentId);
            if ($statement && !empty($statement['StatementNumber'])) {
                $log_statements_db->clear_paid_date((int)$statement['StatementNumber']);
            } else {
                error_log("No statement linked to disputed payment_intent {$paymentIntentId}");
            }
        } else {
            error_log('Dispute payload missing payment_intent reference.');
        }

        http_response_code(200);
        exit();
        break;

    case 'setup_intent.succeeded':

        $setupIntent = $event->data->object; // \Stripe\SetupIntent

        $stripeCustomerId = $setupIntent->customer;        // "cus_..."
        $stripePaymentMethodId = $setupIntent->payment_method; // "pm_..."

        // 1) Fetch PaymentMethod details from Stripe
        try {
            $paymentMethod = \Stripe\PaymentMethod::retrieve($stripePaymentMethodId);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            error_log('Stripe error retrieving PaymentMethod: ' . $e->getMessage());
            http_response_code(200); // avoid retry loop, but log the error
            exit();
        }

        // 2) Extract US bank account details
        $usBank = $paymentMethod->us_bank_account ?? null;

        $bankName   = $usBank->bank_name      ?? null;
        $last4      = $usBank->last4          ?? null;
        $accountType= $usBank->account_type   ?? null; // "checking" / "savings"
        $fingerprint= $usBank->fingerprint    ?? null;
        $accountHolderType= $usBank->account_holder_type   ?? null;

        // 3) Map Stripe customer "cus_..." -> local StripeCustomer row id
        $db = Database::getDB();

        $query = 'SELECT StripeCustomerId FROM StripeCustomers WHERE stripe_customer_id = :stripe_customer LIMIT 1';
        $stmt = $db->prepare($query);
        $stmt->bindValue(':stripe_customer', $stripeCustomerId, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        if (!$row) {
            error_log("No local StripeCustomers row for stripe_customer_id = {$stripeCustomerId}");
            http_response_code(200);
            exit();
        }

        $localStripeCustomerId = (int)$row['StripeCustomerId'];

        // 4) Upsert into PaymentMethods
        // First see if we already know this pm_xxx
        $check = $db->prepare('SELECT StripePaymentMethodId FROM StripePaymentMethods WHERE stripe_payment_method_id = :pm LIMIT 1');
        $check->bindValue(':pm', $stripePaymentMethodId, PDO::PARAM_STR);
        $check->execute();
        $existing = $check->fetch(PDO::FETCH_ASSOC);
        $check->closeCursor();

        if ($existing) {
            $update = $db->prepare('
                UPDATE StripePaymentMethods
                SET 
                    BankName = :bankName,
                    Last4 = :last4,
                    AccountType = :accountType
                    AccountHolderType = :accountHolderType
                WHERE StripePaymentMethodId = :id
            ');
            $update->bindValue(':bankName', $bankName);
            $update->bindValue(':last4', $last4);
            $update->bindValue(':accountType', $accountType);
            $update->bindValue(':accountHolderType', $accountHolderType);
            $update->bindValue(':id', $existing['StripePaymentMethodId'], PDO::PARAM_INT);
            $update->execute();
            $update->closeCursor();
        } else {
            $insert = $db->prepare('
                INSERT INTO StripePaymentMethods 
                    (StripeCustomerId, BankName, Last4, AccountType, AccountHolderType, stripe_payment_method_id, IsEnabled)
                VALUES
                    (:StripeCustomerId, :BankName, :Last4, :AccountType, :AccountHolderType, :stripe_payment_method_id, :IsEnabled)
            ');
            $insert->bindValue(':StripeCustomerId', $localStripeCustomerId, PDO::PARAM_INT);
            $insert->bindValue(':stripe_payment_method_id', $stripePaymentMethodId, PDO::PARAM_STR);
            $insert->bindValue(':IsEnabled', 1);
            $insert->bindValue(':BankName', $bankName);
            $insert->bindValue(':Last4', $last4);
            $insert->bindValue(':AccountType', $accountType);
            $insert->bindValue(':AccountHolderType', $accountHolderType);
            $insert->execute();
            $insert->closeCursor();
        }

        // 5) Optionally mark the latest invite as used for this customer
        $markInvite = $db->prepare('
            UPDATE PaymentMethodInvites
            SET UsedAt = NOW()
            WHERE StripeCustomerId = :StripeCustomerId
            AND UsedAt IS NULL
            AND ExpiresAt > NOW()
            ORDER BY CreatedAt DESC
            LIMIT 1
        ');
        $markInvite->bindValue(':StripeCustomerId', $localStripeCustomerId, PDO::PARAM_INT);
        $markInvite->execute();
        $markInvite->closeCursor();

        http_response_code(200);
        exit();
        break;
}

http_response_code(200);
echo 'OK';
