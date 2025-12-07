<?php 
class CompaniesDB {
    public function get_companies_sudo() {
        $db = Database::getDB();
        $query =  'select c.* 
                  from Company c
                  where c.Deleted is null';
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
                  where c.userId = :userId
                  and c.Deleted is null';
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
    public function update_name ($company_id, $new_name) {
        $db = Database::getDB();
        $query = 'update Company
        set Name = :Name
        where CompanyId = :CompanyId';
        $statement = $db->prepare($query);
        $statement->bindValue(':Name', $new_name);
        $statement->bindValue(':CompanyId', $company_id);
        $statement->execute();
        $statement->closeCursor();
        return true;
    }
    public function update_address1 ($company_id, $new_address1) {
        $db = Database::getDB();
        $query = 'update Company
        set Address1 = :Address1
        where CompanyId = :CompanyId';
        $statement = $db->prepare($query);
        $statement->bindValue(':Address1', $new_address1);
        $statement->bindValue(':CompanyId', $company_id);
        $statement->execute();
        $statement->closeCursor();
        return true;
    }
    public function update_address2 ($company_id, $new_address2) {
        $db = Database::getDB();
        $query = 'update Company
        set Address2 = :Address2
        where CompanyId = :CompanyId';
        $statement = $db->prepare($query);
        $statement->bindValue(':Address2', $new_address2);
        $statement->bindValue(':CompanyId', $company_id);
        $statement->execute();
        $statement->closeCursor();
        return true;
    }
    public function update_address3 ($company_id, $new_address3) {
        $db = Database::getDB();
        $query = 'update Company
        set Address3 = :Address3
        where CompanyId = :CompanyId';
        $statement = $db->prepare($query);
        $statement->bindValue(':Address3', $new_address3);
        $statement->bindValue(':CompanyId', $company_id);
        $statement->execute();
        $statement->closeCursor();
        return true;
    }
    public function update_city ($company_id, $new_city) {
        $db = Database::getDB();
        $query = 'update Company
        set City = :City
        where CompanyId = :CompanyId';
        $statement = $db->prepare($query);
        $statement->bindValue(':City', $new_city);
        $statement->bindValue(':CompanyId', $company_id);
        $statement->execute();
        $statement->closeCursor();
        return true;
    }
    public function update_state_id ($company_id, $new_state_id) {
        $db = Database::getDB();
        $query = 'update Company
        set StateId = :StateId
        where CompanyId = :CompanyId';
        $statement = $db->prepare($query);
        $statement->bindValue(':StateId', $new_state_id);
        $statement->bindValue(':CompanyId', $company_id);
        $statement->execute();
        $statement->closeCursor();
        return true;
    }
    public function update_zip ($company_id, $new_zip) {
        $db = Database::getDB();
        $query = 'update Company
        set Zip = :Zip
        where CompanyId = :CompanyId';
        $statement = $db->prepare($query);
        $statement->bindValue(':Zip', $new_zip);
        $statement->bindValue(':CompanyId', $company_id);
        $statement->execute();
        $statement->closeCursor();
        return true;
    }
    public function delete_company ($company_id, $deleted) {
        $db = Database::getDB();
        $query = 'update Company
        set Deleted = :Deleted
        where CompanyId = :CompanyId';
        $statement = $db->prepare($query);
        $statement->bindValue(':Deleted', $deleted);
        $statement->bindValue(':CompanyId', $company_id);
        $statement->execute();
        $statement->closeCursor();
        return true;
    }
    public function get_company_from_customer($customer_id) {
        $db = Database::getDB();
        $query =    'with all_co as (
                        select co.*,
                        row_number() over(partition by ct.CustomerId order by ContractId desc) as rownum 
                        from Company co 
                        left join Contract ct 
                        on co.CompanyId = ct.CompanyId 
                        where 1=1 
                        and ct.Deleted is null 
                        and ct.CustomerId = :CustomerId
                    )
                    select * from all_co where rownum = 1';
        $statement = $db->prepare($query);
        $statement->bindValue(':CustomerId', $customer_id);
        $statement->execute();
        $company_info = $statement->fetch();
        $statement->closeCursor(); 
        return $company_info;
    }
}

?>
