<?php
class CustomerDB {
    public function get_customers($user_id) {
        $db = Database::getDB();
        $query =  'select c.* 
                  from Customer c
                  where c.userId = :userId';
        $statement = $db->prepare($query);
        $statement->bindValue(':userId', $user_id);
        $statement->execute();
        $customers = $statement->fetchAll();
        $statement->closeCursor(); 
        return $customers;
    }
    public function get_customer_info($customer_id) {
        $db = Database::getDB();
        $query = 'select *
                 from Customer 
                 where CustomerId = :CustomerId';
        $statement = $db->prepare($query);
        $statement->bindValue(':CustomerId', $customer_id);
        $statement->execute();
        $customer_info = $statement->fetch();
        $statement->closeCursor();
        return $customer_info;
    }
}
?>