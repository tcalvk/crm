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
}
?>