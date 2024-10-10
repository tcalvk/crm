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
    public function update_name($customer_id, $new_value) {
        $db = Database::getDB();
        $query = 'update Customer 
                 set Name = :Name
                 where CustomerId = :CustomerId';
        $statement = $db->prepare($query);
        $statement->bindValue(':Name', $new_value);
        $statement->bindValue(':CustomerId', $customer_id);
        $statement->execute();
        $statement->closeCursor();
        return true;
    }
    public function update_address1($customer_id, $new_value) {
        $db = Database::getDB();
        $query = 'update Customer 
                 set Address1 = :Address1
                 where CustomerId = :CustomerId';
        $statement = $db->prepare($query);
        $statement->bindValue(':Address1', $new_value);
        $statement->bindValue(':CustomerId', $customer_id);
        $statement->execute();
        $statement->closeCursor();
        return true;
    }
    public function update_address2($customer_id, $new_value) {
        $db = Database::getDB();
        $query = 'update Customer 
                 set Address2 = :Address2
                 where CustomerId = :CustomerId';
        $statement = $db->prepare($query);
        $statement->bindValue(':Address2', $new_value);
        $statement->bindValue(':CustomerId', $customer_id);
        $statement->execute();
        $statement->closeCursor();
        return true;
    }
    public function update_address3($customer_id, $new_value) {
        $db = Database::getDB();
        $query = 'update Customer 
                 set Address3 = :Address3
                 where CustomerId = :CustomerId';
        $statement = $db->prepare($query);
        $statement->bindValue(':Address3', $new_value);
        $statement->bindValue(':CustomerId', $customer_id);
        $statement->execute();
        $statement->closeCursor();
        return true;
    }
    public function update_city($customer_id, $new_value) {
        $db = Database::getDB();
        $query = 'update Customer 
                 set City = :City
                 where CustomerId = :CustomerId';
        $statement = $db->prepare($query);
        $statement->bindValue(':City', $new_value);
        $statement->bindValue(':CustomerId', $customer_id);
        $statement->execute();
        $statement->closeCursor();
        return true;
    }
    public function update_state_id($customer_id, $new_value) {
        $db = Database::getDB();
        $query = 'update Customer 
                 set StateId = :StateId
                 where CustomerId = :CustomerId';
        $statement = $db->prepare($query);
        $statement->bindValue(':StateId', $new_value);
        $statement->bindValue(':CustomerId', $customer_id);
        $statement->execute();
        $statement->closeCursor();
        return true;
    }
    public function update_zip($customer_id, $new_value) {
        $db = Database::getDB();
        $query = 'update Customer 
                 set Zip = :Zip
                 where CustomerId = :CustomerId';
        $statement = $db->prepare($query);
        $statement->bindValue(':Zip', $new_value);
        $statement->bindValue(':CustomerId', $customer_id);
        $statement->execute();
        $statement->closeCursor();
        return true;
    }
    public function update_phone($customer_id, $new_value) {
        $db = Database::getDB();
        $query = 'update Customer 
                 set Phone = :Phone
                 where CustomerId = :CustomerId';
        $statement = $db->prepare($query);
        $statement->bindValue(':Phone', $new_value);
        $statement->bindValue(':CustomerId', $customer_id);
        $statement->execute();
        $statement->closeCursor();
        return true;
    }
    public function update_email($customer_id, $new_value) {
        $db = Database::getDB();
        $query = 'update Customer 
                 set Email = :Email
                 where CustomerId = :CustomerId';
        $statement = $db->prepare($query);
        $statement->bindValue(':Email', $new_value);
        $statement->bindValue(':CustomerId', $customer_id);
        $statement->execute();
        $statement->closeCursor();
        return true;
    }
    public function get_customers_sudo () {
        $db = Database::getDB();
        $query = 'select c.*
                 from Customer c';
        $statement = $db->prepare($query);
        $statement->execute();
        $customers = $statement->fetchAll();
        $statement->closeCursor();
        return $customers;
    }
    public function create_customer($name, $address1, $address2, $address3, $city, $state_id, $zip, $phone, $email, $user_id) {
        $db = Database::getDB();
        $query = 'insert into Customer (Name, Address1, Address2, Address3, City, StateId, Zip, Phone, Email, userId)
        values (:Name, :Address1, :Address2, :Address3, :City, :StateId, :Zip, :Phone, :Email, :userId)';
        $statement = $db->prepare($query);
        $statement->bindValue(':Name', $name);
        $statement->bindValue(':Address1', $address1);
        $statement->bindValue(':Address2', $address2);
        $statement->bindValue(':Address3', $address3);
        $statement->bindValue(':City', $city);
        $statement->bindValue(':StateId', $state_id);
        $statement->bindValue(':Zip', $zip);
        $statement->bindValue(':Phone', $phone);
        $statement->bindValue(':Email', $email);
        $statement->bindValue(':userId', $user_id);
        $statement->execute();
        $statement->closeCursor();
        return true;
    }
}
?>