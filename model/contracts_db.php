<?php 
class ContractsDB {
    public function get_evergreen_15() {
        $db = Database::getDB();
        $query =  'select c.ContractId, p.PropertyId, c.BaseAmt, c.CAM, c.DueDate, cp.Name "CompanyName", cp.Address1 "CompanyAddress1",
                  cp.Address2 "CompanyAddress2", cp.Address3 "CompanyAddress3", cp.City "CompanyCity", cp.StateId
                  "CompanyState", cp.Zip "CompanyZip", ct.Name "BillingName", ct.Attention, ct.Address1 "BillingAddress1", 
                  ct.Address2 "BillingAddress2", ct.Address3 "BillingAddress3", ct.City "BillingCity", ct.StateId "BillingState", 
                  ct.Zip "BillingZip", ct.Email "BillingEmail", c.BaseAmt + c.CAM "Total"
                  from Contract c 
                  left join Property p on c.PropertyId = p.PropertyId
                  left join Company cp on p.OwnedBy = cp.CompanyId
                  left join Customer ct on c.CustomerId = ct.CustomerId
                  where c.ContractType = "Evergreen"
                  and c.StatementSendDate = 15';
        $statement = $db->prepare($query);
        $statement->execute();
        $contracts = $statement->fetchAll();
        $statement->closeCursor(); 
        return $contracts;
    }
}

?>