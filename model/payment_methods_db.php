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
}