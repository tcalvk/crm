<?php
class PaymentMethodsDB {
    public function get_payment_methods_limit3($customer_id) {
        $db = Database::getDB();
        $query = 'select 
                    pm.*
                 from StripePaymentMethods pm
                 left join StripeCustomers sc 
                    on pm.StripeCustomerId = sc.StripeCustomerId
                 where sc.CustomerId = :CustomerId
                 limit 3';
        $statement = $db->prepare($query);
        $statement->bindValue(':CustomerId', $customer_id);
        $statement->execute();
        $payment_methods = $statement->fetchAll();
        $statement->closeCursor();
        return $payment_methods;
    }

    public function get_payment_methods($customer_id) {
        $db = Database::getDB();
        $query = 'select 
                    pm.*
                 from StripePaymentMethods pm
                 left join StripeCustomers sc 
                    on pm.StripeCustomerId = sc.StripeCustomerId
                 where sc.CustomerId = :CustomerId';
        $statement = $db->prepare($query);
        $statement->bindValue(':CustomerId', $customer_id);
        $statement->execute();
        $payment_methods = $statement->fetchAll();
        $statement->closeCursor();
        return $payment_methods;
    }

    public function set_payment_method_enabled(int $payment_method_id, int $is_enabled): bool {
        $db = Database::getDB();
        $query = '
            update StripePaymentMethods
            set IsEnabled = :IsEnabled, UpdatedAt = now()
            where StripePaymentMethodId = :StripePaymentMethodId
        ';
        $statement = $db->prepare($query);
        $statement->bindValue(':IsEnabled', $is_enabled, PDO::PARAM_INT);
        $statement->bindValue(':StripePaymentMethodId', $payment_method_id, PDO::PARAM_INT);
        $statement->execute();
        $statement->closeCursor();
        return true;
    }

    public function delete_payment_method(int $payment_method_id): bool {
        $db = Database::getDB();
        $query = 'delete from StripePaymentMethods where StripePaymentMethodId = :StripePaymentMethodId';
        $statement = $db->prepare($query);
        $statement->bindValue(':StripePaymentMethodId', $payment_method_id, PDO::PARAM_INT);
        $statement->execute();
        $statement->closeCursor();
        return true;
    }
}
