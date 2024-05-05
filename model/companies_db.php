<?php 
class CompaniesDB {
    public function get_companies_sudo() {
        $db = Database::getDB();
        $query =  'select c.* 
                  from Company c';
        $statement = $db->prepare($query);
        $statement->execute();
        $companies = $statement->fetchAll();
        $statement->closeCursor(); 
        return $companies;
    }
    public function get_companies($user_id) {
        $db = Database::getDB();
        $query =  'select c.* 
                  from Company c
                  where c.userId = :userId';
        $statement = $db->prepare($query);
        $statement->bindValue(':userId', $user_id);
        $statement->execute();
        $companies = $statement->fetchAll();
        $statement->closeCursor(); 
        return $companies;
    }
    public function get_company_info($company_id) {
        $db = Database::getDB();
        $query = 'select *
                 from Company  
                 where CompanyId = :CompanyId';
        $statement = $db->prepare($query);
        $statement->bindValue(':CompanyId', $company_id);
        $statement->execute();
        $company_info = $statement->fetch();
        $statement->closeCursor();
        return $company_info;
    }
    public function create_company($name, $address1, $address2, $address3, $city, $state_id, $zip, $user_id) {
        $db = Database::getDB();
        $query = "insert into Company (Name, Address1, Address2, Address3, City, StateId, Zip, userId)
        values (:Name, :Address1, :Address2, :Address3, :City, :StateId, :Zip, :userId)";
        $statement = $db->prepare($query);
        $statement->bindValue(':Name', $name);
        $statement->bindValue(':Address1', $address1);
        $statement->bindValue(':Address2', $address2);
        $statement->bindValue(':Address3', $address3);
        $statement->bindValue(':City', $city);
        $statement->bindValue(':StateId', $state_id);
        $statement->bindValue(':Zip', $zip);
        $statement->bindValue(':userId', $user_id);
        $statement->execute();
        $statement->closeCursor();
        return true;
    }
}

?>