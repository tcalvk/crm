<?php 
class ContractsDB {
    public function get_evergreen_contracts() {
        $db = Database::getDB();
        $query =  'select c.ContractId, p.PropertyId, c.BaseAmt, c.DueDate, cp.Name "CompanyName", cp.Address1 "CompanyAddress1",
                  cp.Address2 "CompanyAddress2", cp.Address3 "CompanyAddress3", cp.City "CompanyCity", cp.StateId
                  "CompanyState", cp.Zip "CompanyZip"
                  from Contract c 
                  left join Property p on c.PropertyId = p.PropertyId
                  left join Company cp on p.OwnedBy = cp.CompanyId
                  left join Customer ct on c.CustomerId = ct.CustomerId
                  where c.ContractType = "Evergreen"';
        $statement = $db->prepare($query);
        $statement->execute();
        $contracts = $statement->fetchAll();
        $statement->closeCursor(); 
        return $contracts;
    }
}

?>