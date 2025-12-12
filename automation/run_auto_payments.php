<?php 

error_reporting(E_ALL);
ini_set('display_errors', '1');

// This section is for cli args
///////////////////////////////

$options = getopt("", [
    "run_mode::"
]);

$run_mode   = $options['run_mode'] ?? 'dev';

echo "Run Mode: $run_mode\n";

///////////////////////////////
///////////////////////////////

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../model/database.php';
require __DIR__ . '/../model/log_statements_db.php';
require_once __DIR__ . '/../model/BillingService.php';
use Stripe\Stripe;

$log_statements_db = new LogStatementsDB;
$config = require __DIR__ . '/../config/stripe_dev.php';

$stripeSecret = $config['stripe_secret_key'];
$webhookSecret = $config['webhook_secret'];

\Stripe\Stripe::setApiKey($stripeSecret);

try {
    echo "Fetching statements for auto-billing...\n";

    if ($run_mode == 'dev') {
        $statements = $log_statements_db->get_statements_due_auto_billing_test();
    } elseif ($run_mode == 'prod') {
        $statements = $log_statements_db->get_statements_due_auto_billing();
    } else {
        throw new InvalidArgumentException("Unsupported run mode: {$run_mode}");
    }

    echo "Found " . count($statements) . " statements.\n";

    foreach ($statements as $statement) {
        // For now, just dump one so we see the shape
        print_r($statement);
        echo "-----------------------\n";

        // Convert dollars to cents:
        $amountCents = (int) round($statement['TotalAmt'] * 100);

        $payment_intent = BillingService::createAchPaymentIntent(
            $statement['stripe_customer_id'],
            $statement['stripe_payment_method_id'],
            $amountCents,
            (int)$statement['StatementNumber'],
            isset($statement['ContractId']) ? (int)$statement['ContractId'] : null,
            'ACH payment for Statement #' . $statement['StatementNumber'],
            $run_mode
        );

        $piArray = $payment_intent->toArray();              // Stripe helper
        $piJson  = json_encode($piArray, JSON_UNESCAPED_UNICODE);

        BillingService::create_stripe_event($piJson);

        $currency = 'USD'; // This probably needs to be changed later if we ever implement currency for contracts

        BillingService::create_stripe_payment(
            (int)$statement['StripeCustomerId'],
            (int)$statement['StripePaymentMethodId'],
            (float)$amountCents,
            $currency,
            $payment_intent->status,
            $payment_intent->id
        );

        $stripe_payment = BillingService::get_stripe_payment_from_stripe_id($payment_intent->id);
        $StripePaymentId = $stripe_payment['StripePaymentId'];

        $add_payment_method_id = $log_statements_db->add_stripe_payment_id($statement['StatementNumber'], $StripePaymentId);
    }
} catch (Throwable $e) {
    echo 'Auto payments creation failed: ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
