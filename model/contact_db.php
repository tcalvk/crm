<?php 
class ContactDB {
    public function get_contacts($contract_id) {
        $db = Database::getDB();
        $query =  'select c.* 
                  from Contact c
                  left join Customer cu on c.CustomerId = cu.CustomerId
                  left join Contract ct on cu.CustomerId = ct.CustomerId 
                  where ct.ContractId = :ContractId
                  and c.ReceiveStatements = 1';
        $statement = $db->prepare($query);
        $statement->bindValue(':ContractId', $contract_id);
        $statement->execute();
        $contacts = $statement->fetchAll();
        $statement->closeCursor(); 
        return $contacts;
    }
}

?>