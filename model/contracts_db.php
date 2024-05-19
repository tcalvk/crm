<?php 
class ContractsDB {
    public function get_evergreen_15() {
        $db = Database::getDB();
        $query =  'select c.ContractId, p.PropertyId, ctt.BaseAmt, c.CAM, c.DueDate, cp.Name "CompanyName", cp.Address1 "CompanyAddress1",
        cp.Address2 "CompanyAddress2", cp.Address3 "CompanyAddress3", cp.City "CompanyCity", cp.StateId
        "CompanyState", cp.Zip "CompanyZip", ct.Name "BillingName", ct.Attention, ct.Address1 "BillingAddress1", 
        ct.Address2 "BillingAddress2", ct.Address3 "BillingAddress3", ct.City "BillingCity", ct.StateId "BillingState", 
        ct.Zip "BillingZip", ct.Email "BillingEmail", c.BaseAmt + c.CAM "Total", u.email "ContractOwnerEmail"
        from Contract c 
        left join Property p on c.PropertyId = p.PropertyId
        left join Company cp on p.OwnedBy = cp.CompanyId
        left join Customer ct on c.CustomerId = ct.CustomerId
        left join users u on ct.userId = u.userId
        left join ContractTerm ctt on c.ContractId = ctt.ContractId
        where c.ContractType = "Evergreen"
        and c.StatementSendDate = 15
        and (c.TestContract is null or c.TestContract = 0)
        and str_to_date(concat(year(adddate(current_date(), interval 1 month)), "-", month(adddate(current_date(), interval 1 month)), "-", c.DueDate), "%Y-%m-%d") >= ctt.TermStartDate
        and str_to_date(concat(year(adddate(current_date(), interval 1 month)), "-", month(adddate(current_date(), interval 1 month)), "-", c.DueDate), "%Y-%m-%d") <= ctt.TermEndDate
        and c.Deleted is null';
        $statement = $db->prepare($query);
        $statement->execute();
        $contracts = $statement->fetchAll();
        $statement->closeCursor(); 
        return $contracts;
    }
    public function get_fixed_1() {
        $db = Database::getDB();
        $query =  'select c.ContractId, p.PropertyId, c.BaseAmt, c.CAM, c.DueDate, cp.Name "CompanyName", cp.Address1 "CompanyAddress1",
                  cp.Address2 "CompanyAddress2", cp.Address3 "CompanyAddress3", cp.City "CompanyCity", cp.StateId
                  "CompanyState", cp.Zip "CompanyZip", ct.Name "BillingName", ct.Attention, ct.Address1 "BillingAddress1", 
                  ct.Address2 "BillingAddress2", ct.Address3 "BillingAddress3", ct.City "BillingCity", ct.StateId "BillingState", 
                  ct.Zip "BillingZip", ct.Email "BillingEmail", c.BaseAmt + c.CAM "Total", p.Name "PropertyName", c.NumPaymentsDue, 
                  c.TotalPaymentsDue, u.email "ContractOwnerEmail"
                  from Contract c 
                  left join Property p on c.PropertyId = p.PropertyId
                  left join Company cp on p.OwnedBy = cp.CompanyId
                  left join Customer ct on c.CustomerId = ct.CustomerId
                  left join users u on ct.userId = u.userId
                  where c.ContractType = "Fixed"
                  and c.StatementSendDate = 1
                  and (c.TestContract is null or c.TestContract = 0)
                  and c.Deleted is null';
        $statement = $db->prepare($query);
        $statement->execute();
        $contracts = $statement->fetchAll();
        $statement->closeCursor(); 
        return $contracts;
    }
    public function update_contract($contract_id, $new_payments_due) {
        $db = Database::getDB();
        $query = 'update Contract
                 set NumPaymentsDue = :NewPaymentsDue
                 where ContractId = :ContractId';
        $statement = $db->prepare($query);
        $statement->bindValue(':NewPaymentsDue', $new_payments_due);
        $statement->bindValue(':ContractId', $contract_id);
        $statement->execute();
        $statement->closeCursor();
        return true;
    }
    public function get_test_evergreen_contracts() {
        $db = Database::getDB();
        $query =  'select c.ContractId, p.PropertyId, ctt.BaseAmt, c.CAM, c.DueDate, cp.Name "CompanyName", cp.Address1 "CompanyAddress1",
        cp.Address2 "CompanyAddress2", cp.Address3 "CompanyAddress3", cp.City "CompanyCity", cp.StateId
        "CompanyState", cp.Zip "CompanyZip", ct.Name "BillingName", ct.Attention, ct.Address1 "BillingAddress1", 
        ct.Address2 "BillingAddress2", ct.Address3 "BillingAddress3", ct.City "BillingCity", ct.StateId "BillingState", 
        ct.Zip "BillingZip", ct.Email "BillingEmail", ctt.BaseAmt + c.CAM "Total", p.Name "PropertyName", c.NumPaymentsDue, 
        c.TotalPaymentsDue, u.email "ContractOwnerEmail"
        from Contract c 
        left join Property p on c.PropertyId = p.PropertyId
        left join Company cp on p.OwnedBy = cp.CompanyId
        left join Customer ct on c.CustomerId = ct.CustomerId
        left join users u on ct.userId = u.userId
        right join ContractTerm ctt on c.ContractId = ctt.ContractId
        where c.TestContract = 1
        and c.ContractType = "Evergreen"
        and CURRENT_DATE >= ctt.TermStartDate 
        and CURRENT_DATE <= ctt.TermEndDate';
        $statement = $db->prepare($query);
        $statement->execute();
        $contracts = $statement->fetchAll();
        $statement->closeCursor(); 
        return $contracts;
    }
    public function get_test_fixed_contracts() {
        $db = Database::getDB();
        $query =  'select c.ContractId, p.PropertyId, c.BaseAmt, c.CAM, c.DueDate, cp.Name "CompanyName", cp.Address1 "CompanyAddress1",
                  cp.Address2 "CompanyAddress2", cp.Address3 "CompanyAddress3", cp.City "CompanyCity", cp.StateId
                  "CompanyState", cp.Zip "CompanyZip", ct.Name "BillingName", ct.Attention, ct.Address1 "BillingAddress1", 
                  ct.Address2 "BillingAddress2", ct.Address3 "BillingAddress3", ct.City "BillingCity", ct.StateId "BillingState", 
                  ct.Zip "BillingZip", ct.Email "BillingEmail", c.BaseAmt + c.CAM "Total", p.Name "PropertyName", c.NumPaymentsDue, 
                  c.TotalPaymentsDue, u.email "ContractOwnerEmail"
                  from Contract c 
                  left join Property p on c.PropertyId = p.PropertyId
                  left join Company cp on p.OwnedBy = cp.CompanyId
                  left join Customer ct on c.CustomerId = ct.CustomerId
                  left join users u on ct.userId = u.userId
                  where c.TestContract = 1
                  and c.ContractType = "Fixed"';
        $statement = $db->prepare($query);
        $statement->execute();
        $contracts = $statement->fetchAll();
        $statement->closeCursor(); 
        return $contracts;
    }
    public function get_contract_info($contract_id) {
        $db = Database::getDB();
        $query = 'select c.*, cu.userId, cu.Name "CustomerName", p.Name "PropertyName", co.Name "CompanyName"
                 from Contract c 
                 left join Customer cu on c.CustomerId = cu.CustomerId 
                 left join Property p on c.PropertyId = p.PropertyId 
                 left join Company co on c.CompanyId = co.CompanyId
                 where c.ContractId = :ContractId';
        $statement = $db->prepare($query);
        $statement->bindValue(':ContractId', $contract_id);
        $statement->execute();
        $contract_info = $statement->fetch();
        $statement->closeCursor();
        return $contract_info;
    }
    public function get_contracts_limit3($customer_id) {
        $db = Database::getDB();
        $query = 'select c.*
                 from Contract c 
                 where c.CustomerId = :CustomerId
                 limit 3';
        $statement = $db->prepare($query);
        $statement->bindValue(':CustomerId', $customer_id);
        $statement->execute();
        $contracts = $statement->fetchAll();
        $statement->closeCursor();
        return $contracts;
    }
    public function get_contracts($customer_id) {
        $db = Database::getDB();
        $query = 'select c.*
                 from Contract c 
                 where c.CustomerId = :CustomerId';
        $statement = $db->prepare($query);
        $statement->bindValue(':CustomerId', $customer_id);
        $statement->execute();
        $contracts = $statement->fetchAll();
        $statement->closeCursor();
        return $contracts;
    }
    public function update_statementautoreceive($contract_id, $col_val) {
        $db = Database::getDB();
        $query = 'update Contract
        set StatementAutoReceive = :ColVal
        where ContractId = :ContractId';
        $statement = $db->prepare($query);
        $statement->bindValue(':ColVal', $col_val);
        $statement->bindValue(':ContractId', $contract_id);
        $statement->execute();
        $statement->closeCursor();
        return true;
    } public function get_active_contracts_by_company($company_id) {
        $db = Database::getDB();
        $query = 'select c.ContractId
        from Contract c 
        where c.CompanyId = :CompanyId
        and c.Deleted is null';
        $statement = $db->prepare($query);
        $statement->bindValue(':CompanyId', $company_id);
        $statement->execute();
        $contracts = $statement->fetchAll();
        $statement->closeCursor();
        return $contracts;
    }
}

?>