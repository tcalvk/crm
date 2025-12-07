<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/database.php';

use Stripe\Customer;
use Stripe\SetupIntent;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class BillingService
{
    private static function getStripeSecretKey(): string
    {
        $configPath = __DIR__ . '/../config/stripe_config.php';
        if (!file_exists($configPath)) {
            $configPath = __DIR__ . '/../config/stripe_dev.php';
        }
        $config = require $configPath;
        return $config['stripe_secret_key'];
    }

    private static function getInviteHashKey(): string
    {
        // Reuse Stripe secret key as the HMAC secret to keep configuration simple.
        // Swap to a dedicated secret if you prefer.
        return self::getStripeSecretKey();
    }

    private static function generateInviteCode(int $length = 12): string
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // no confusing chars
        $max = strlen($alphabet) - 1;
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $alphabet[random_int(0, $max)];
        }
        return $code;
    }

    private static function normalizeCode(string $code): string
    {
        $normalized = preg_replace('/[^a-zA-Z0-9]/', '', $code);
        return strtoupper($normalized ?? '');
    }

    private static function hashInviteCode(string $code): string
    {
        $normalized = self::normalizeCode($code);
        return hash_hmac('sha256', $normalized, self::getInviteHashKey());
    }

    private static function getStripeCustomerRowByUserId(int $customerId)
    {
        $db = Database::getDB();
        $query = 'SELECT * FROM StripeCustomers WHERE CustomerId = :CustomerId LIMIT 1';
        $statement = $db->prepare($query);
        $statement->bindValue(':CustomerId', $customerId);
        $statement->execute();
        $existing = $statement->fetch();
        $statement->closeCursor();
        return $existing ?: null;
    }

    private static function getStripeCustomerRowById(int $rowId)
    {
        $db = Database::getDB();
        // Support StripeCustomerId as the primary key column.
        $query = 'SELECT * FROM StripeCustomers WHERE StripeCustomerId = :id LIMIT 1';
        $statement = $db->prepare($query);
        $statement->bindValue(':id', $rowId, \PDO::PARAM_INT);
        $statement->execute();
        $existing = $statement->fetch();
        $statement->closeCursor();
        return $existing ?: null;
    }

    public static function ensureStripeCustomerRow($customerId, $email, $name = null)
    {
        Stripe::setApiKey(self::getStripeSecretKey());

        $existing = self::getStripeCustomerRowByUserId($customerId);
        if ($existing && !empty($existing['stripe_customer_id'])) {
            return $existing;
        }

        $customerData = ['email' => $email];
        if (!empty($name)) {
            $customerData['name'] = $name;
        }

        try {
            $customer = Customer::create($customerData);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new \Exception('Stripe error creating customer: ' . $e->getMessage(), $e->getCode(), $e);
        }

        $db = Database::getDB();
        $insert = 'INSERT INTO StripeCustomers (CustomerId, stripe_customer_id, CreatedAt) VALUES (:CustomerId, :stripe_customer_id, NOW())';
        $insertStatement = $db->prepare($insert);
        $insertStatement->bindValue(':CustomerId', $customerId);
        $insertStatement->bindValue(':stripe_customer_id', $customer->id);
        $insertStatement->execute();
        $insertStatement->closeCursor();

        $insertId = $db->lastInsertId();

        return [
            'StripeCustomerId' => $insertId,
            'userId' => $userId,
            'stripe_customer_id' => $customer->id,
        ];
    }

    public static function ensureStripeCustomer($userId, $email, $name = null)
    {
        $row = self::ensureStripeCustomerRow($userId, $email, $name);
        return $row['stripe_customer_id'];
    }

    public static function createBankAccountSetupIntent($userId, $email, $name = null)
    {
        Stripe::setApiKey(self::getStripeSecretKey());

        $stripeCustomerId = self::ensureStripeCustomer($userId, $email, $name);

        try {
            $setupIntent = SetupIntent::create([
                'customer' => $stripeCustomerId,
                'payment_method_types' => ['us_bank_account'],
                'payment_method_options' => [
                    'us_bank_account' => [
                        // Keep verification automatic so Stripe handles microdeposits vs. instant verification.
                        'verification_method' => 'automatic',
                    ],
                ],
            ]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new \Exception('Stripe error creating SetupIntent: ' . $e->getMessage(), $e->getCode(), $e);
        }

        return $setupIntent;
    }

    public static function createBankAccountSetupIntentForStripeCustomer(string $stripeCustomerId)
    {
        Stripe::setApiKey(self::getStripeSecretKey());

        try {
            $setupIntent = SetupIntent::create([
                'customer' => $stripeCustomerId,
                'payment_method_types' => ['us_bank_account'],
                'payment_method_options' => [
                    'us_bank_account' => [
                        'verification_method' => 'automatic',
                    ],
                ],
            ]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new \Exception('Stripe error creating SetupIntent: ' . $e->getMessage(), $e->getCode(), $e);
        }

        return $setupIntent;
    }

    public static function createPaymentMethodInviteForCustomer(int $stripeCustomerRowId): string
    {
        $db = Database::getDB();
        $code = self::generateInviteCode(12);
        $hash = self::hashInviteCode($code);
        $codeLast4 = substr($code, -4);

        $query = 'INSERT INTO PaymentMethodInvites (StripeCustomerId, CodeHash, CodeLast4, ExpiresAt, CreatedAt, UsedAt)
                  VALUES (:StripeCustomerId, :CodeHash, :CodeLast4, DATE_ADD(NOW(), INTERVAL 7 DAY), NOW(), NULL)';
        $statement = $db->prepare($query);
        $statement->bindValue(':StripeCustomerId', $stripeCustomerRowId, \PDO::PARAM_INT);
        $statement->bindValue(':CodeHash', $hash);
        $statement->bindValue(':CodeLast4', $codeLast4);
        $statement->execute();
        $statement->closeCursor();

        return $code;
    }

    public static function verifyInviteCode(string $code)
    {
        $db = Database::getDB();
        $hash = self::hashInviteCode($code);

        $query = 'SELECT * FROM PaymentMethodInvites 
                  WHERE CodeHash = :CodeHash 
                  AND UsedAt IS NULL 
                  AND ExpiresAt > NOW()
                  LIMIT 1';
        $statement = $db->prepare($query);
        $statement->bindValue(':CodeHash', $hash);
        $statement->execute();
        $invite = $statement->fetch();
        $statement->closeCursor();

        if (!$invite) {
            return null;
        }

        return $invite;
    }

    public static function getActiveInviteById(int $inviteId)
    {
        $db = Database::getDB();
        $query = 'SELECT * FROM PaymentMethodInvites 
                  WHERE PaymentMethodInviteId = :id 
                  AND UsedAt IS NULL 
                  AND ExpiresAt > NOW()
                  LIMIT 1';
        $statement = $db->prepare($query);
        $statement->bindValue(':id', $inviteId, \PDO::PARAM_INT);
        $statement->execute();
        $invite = $statement->fetch();
        $statement->closeCursor();

        return $invite ?: null;
    }

    public static function getStripeCustomerIdForInvite(array $invite)
    {
        $rowId = $invite['StripeCustomerId'] ?? null;
        if ($rowId === null) {
            return null;
        }

        // If the invite already stores a Stripe customer string (legacy data), return it directly.
        if (is_string($rowId) && strpos($rowId, 'cus_') === 0) {
            return $rowId;
        }

        $row = self::getStripeCustomerRowById((int) $rowId);
        if (!$row || empty($row['stripe_customer_id'])) {
            return null;
        }
        return $row['stripe_customer_id'];
    }

    public static function createAchPaymentIntent(
        string $stripeCustomerId,
        string $stripePaymentMethodId,
        int $amountCents,
        int $statementId,
        ?int $contractId = null,
        ?string $description = null,
        string $run_mode
    ): \Stripe\PaymentIntent 
    {
        if ($amountCents <= 0) {
            throw new \InvalidArgumentException('Amount must be a positive integer (cents).');
        }

        if ($description === null) {
            $description = 'ACH payment for Statement #' . $statementId;
        }

        // Build params for PaymentIntent normally
        $intentParams = [
            'amount' => $amountCents,
            'currency' => 'usd',
            'customer' => $stripeCustomerId,
            'payment_method' => $stripePaymentMethodId,
            'payment_method_types' => ['us_bank_account'],
            'off_session' => true,
            'confirm' => true,
            'description' => $description,
            'metadata' => [
                'statement_id' => (string)$statementId,
                'contract_id'  => $contractId !== null ? (string)$contractId : '',
                'source'       => 'corsaire_crm_scheduled_ach',
            ],
        ];

        // Build request options (idempotency only in prod)
        $requestOptions = [];
        if ($run_mode !== 'dev') {
            $requestOptions['idempotency_key'] = 'statement_' . $statementId . '_ach';
        }

        try {
            // If $requestOptions is empty, Stripe::create() will simply ignore it
            return \Stripe\PaymentIntent::create($intentParams, $requestOptions);

        } catch (\Stripe\Exception\ApiErrorException $e) {
            error_log(sprintf(
                'Stripe ACH PaymentIntent error for statement %d (customer %s, pm %s): %s',
                $statementId,
                $stripeCustomerId,
                $stripePaymentMethodId,
                $e->getMessage()
            ));
            throw $e;
        }
    }

    public static function create_stripe_payment(
        int $StripeCustomerId,
        int $stripePaymentMethodId,
        float $amount_cents,
        string $currency_code,
        string $status,
        string $stripe_payment_intent_id
    ) {
        $db = Database::getDB();
        $query = 'INSERT INTO   StripePayments (
                                    StripeCustomerId, 
                                    StripePaymentMethodId, 
                                    AmountCents, 
                                    CurrencyCode,
                                    Status,
                                    stripe_payment_intent_id,
                                    CreatedAt, 
                                    UpdatedAt
                                )
                  VALUES (:StripeCustomerId, :StripePaymentMethodId, :AmountCents, :CurrencyCode, :Status, :stripe_payment_intent_id,
                  NOW(), NOW())';
        $statement = $db->prepare($query);
        $statement->bindValue(':StripeCustomerId', $StripeCustomerId);
        $statement->bindValue(':StripePaymentMethodId', $stripePaymentMethodId);
        $statement->bindValue(':AmountCents', $amount_cents);
        $statement->bindValue(':CurrencyCode', $currency_code);
        $statement->bindValue(':Status', $status);
        $statement->bindValue(':stripe_payment_intent_id', $stripe_payment_intent_id);
        $statement->execute();
        $statement->closeCursor();

        return true;
    }

    public static function get_stripe_payment_from_stripe_id (
        string $stripe_payment_intent_id
    ) {
        $db = Database::getDB();
        $query = ' 
            select *
            from StripePayments 
            where stripe_payment_intent_id = :stripe_payment_intent_id
        ';
        $statement = $db->prepare($query);
        $statement->bindValue(':stripe_payment_intent_id', $stripe_payment_intent_id);
        $statement->execute();
        $stripe_payment = $statement->fetch();
        $statement->closeCursor();

        return $stripe_payment;
    }

    public static function update_stripe_payment_status(
        string $stripe_payment_intent_id,
        string $status
    ): bool {
        $db = Database::getDB();
        $query = '
            update StripePayments
            set Status = :Status,
                UpdatedAt = NOW()
            where stripe_payment_intent_id = :stripe_payment_intent_id
        ';
        $statement = $db->prepare($query);
        $statement->bindValue(':Status', $status);
        $statement->bindValue(':stripe_payment_intent_id', $stripe_payment_intent_id);
        $statement->execute();
        $rows = $statement->rowCount();
        $statement->closeCursor();

        return $rows > 0;
    }

    public static function get_statement_for_payment_intent(
        string $stripe_payment_intent_id
    ) {
        $db = Database::getDB();
        $query = '
            select 
                ls.StatementNumber,
                ls.TotalAmt,
                ls.PaymentAmount,
                ls.PaidDate,
                sp.StripePaymentId,
                sp.AmountCents
            from StripePayments sp
            left join LogStatements ls on ls.StripePaymentId = sp.StripePaymentId
            where sp.stripe_payment_intent_id = :stripe_payment_intent_id
            limit 1
        ';
        $statement = $db->prepare($query);
        $statement->bindValue(':stripe_payment_intent_id', $stripe_payment_intent_id);
        $statement->execute();
        $result = $statement->fetch();
        $statement->closeCursor();

        return $result ?: null;
    }

    public static function create_stripe_event ($event_data) {
        $db = Database::getDB();
        $query = 'INSERT INTO   StripeEvents (
                                    EventData, 
                                    CreatedAt
                                )
                  VALUES (:EventData, NOW())';
        $statement = $db->prepare($query);
        $statement->bindValue(':EventData', $event_data);
        $statement->execute();
        $statement->closeCursor();

        return true;
    }
}
