<?php 
class ContractsDB {
    public function get_evergreen_15() {
        $db = Database::getDB();
        $query =  'select c.ContractId, p.PropertyId, ctt.BaseAmt, c.CAM, c.DueDate, cp.Name "CompanyName", cp.Address1 "CompanyAddress1",
        cp.Address2 "CompanyAddress2", cp.Address3 "CompanyAddress3", cp.City "CompanyCity", cp.StateId
        "CompanyState", cp.Zip "CompanyZip", ct.Name "BillingName", ct.Attention, ct.Address1 "BillingAddress1", 
        ct.Address2 "BillingAddress2", ct.Address3 "BillingAddress3", ct.City "BillingCity", ct.StateId "BillingState", 
        ct.Zip "BillingZip", ct.Email "BillingEmail", ctt.BaseAmt + c.CAM "Total", u.email "ContractOwnerEmail"
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
        $query = 'select 
                    c.*, 
                    cu.userId, 
                    cu.Name "CustomerName", 
                    p.Name "PropertyName", 
                    co.Name "CompanyName",
                    pm.StripePaymentMethodId,
                    pm.StripeCustomerId,
                    pm.stripe_payment_method_id,
                    pm.AccountType,
                    pm.BankName,
                    pm.Last4,
                    pm.AccountHolderType,
                    pm.IsEnabled,
                    pm.UpdatedAt
                 from Contract c 
                 left join Customer cu on c.CustomerId = cu.CustomerId 
                 left join Property p on c.PropertyId = p.PropertyId 
                 left join Company co on c.CompanyId = co.CompanyId
                 left join StripePaymentMethods pm on c.StripePaymentMethodId = pm.StripePaymentMethodId
                 where c.ContractId = :ContractId';
        $statement = $db->prepare($query);
        $statement->bindValue(':ContractId', $contract_id);
        $statement->execute();
        $contract_info = $statement->fetch();
        $statement->closeCursor();
        return $contract_info;
    }
    public function get_contract($contract_id) {
        $db = Database::getDB();
        $query = 'select * from Contract where ContractId = :ContractId';
        $statement = $db->prepare($query);
        $statement->bindValue(':ContractId', $contract_id, PDO::PARAM_INT);
        $statement->execute();
        $contract = $statement->fetch();
        $statement->closeCursor();
        return $contract;
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
    public function get_contracts_by_customer($customer_id, $limit = null) {
        $db = Database::getDB();
        $query = 'select *
                 from Contract
                 where CustomerId = :CustomerId
                 order by Name';
        if ($limit !== null) {
            $query .= ' limit :limit';
        }
        $statement = $db->prepare($query);
        $statement->bindValue(':CustomerId', $customer_id, PDO::PARAM_INT);
        if ($limit !== null) {
            $statement->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        }
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
    }

    public function update_payment_method($contract_id, $stripe_payment_method_id) {
        $db = Database::getDB();
        $query = 'update Contract
        set StripePaymentMethodId = :StripePaymentMethodId
        where ContractId = :ContractId';
        $statement = $db->prepare($query);
        $statement->bindValue(':StripePaymentMethodId', $stripe_payment_method_id, PDO::PARAM_INT);
        $statement->bindValue(':ContractId', $contract_id, PDO::PARAM_INT);
        $statement->execute();
        $statement->closeCursor();
        return true;
    } 
    
    public function get_active_contracts_by_company($company_id) {
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

    public function create_contract($data) {
        $db = Database::getDB();
        $query = 'insert into Contract
                (Name, PropertyId, CustomerId, CompanyId, BaseAmt, CAM, BillingCycleStart, BillingCycleEnd, DueDate, LateDate, LateFee, StatementSendDate, NumPaymentsDue, TotalPaymentsDue, ContractType, TestContract, StatementAutoReceive, StripePaymentMethodId)
                values
                (:Name, :PropertyId, :CustomerId, :CompanyId, :BaseAmt, :CAM, :BillingCycleStart, :BillingCycleEnd, :DueDate, :LateDate, :LateFee, :StatementSendDate, :NumPaymentsDue, :TotalPaymentsDue, :ContractType, :TestContract, :StatementAutoReceive, :StripePaymentMethodId)';
        $statement = $db->prepare($query);
        $statement->bindValue(':Name', $data['Name']);
        $statement->bindValue(':PropertyId', $data['PropertyId'], PDO::PARAM_INT);
        $statement->bindValue(':CustomerId', $data['CustomerId'], PDO::PARAM_INT);
        $statement->bindValue(':CompanyId', $data['CompanyId'], PDO::PARAM_INT);
        $statement->bindValue(':BaseAmt', $data['BaseAmt']);
        $statement->bindValue(':CAM', $data['CAM']);
        $statement->bindValue(':BillingCycleStart', $data['BillingCycleStart'], PDO::PARAM_INT);
        $statement->bindValue(':BillingCycleEnd', $data['BillingCycleEnd']);
        $statement->bindValue(':DueDate', $data['DueDate']);
        $statement->bindValue(':LateDate', $data['LateDate'], PDO::PARAM_INT);
        $statement->bindValue(':LateFee', $data['LateFee']);
        $statement->bindValue(':StatementSendDate', $data['StatementSendDate'], PDO::PARAM_INT);
        $statement->bindValue(':NumPaymentsDue', $data['NumPaymentsDue'], PDO::PARAM_INT);
        $statement->bindValue(':TotalPaymentsDue', $data['TotalPaymentsDue'], PDO::PARAM_INT);
        $statement->bindValue(':ContractType', $data['ContractType']);
        $statement->bindValue(':TestContract', $data['TestContract']);
        $statement->bindValue(':StatementAutoReceive', $data['StatementAutoReceive']);
        $statement->bindValue(':StripePaymentMethodId', $data['StripePaymentMethodId'], PDO::PARAM_INT);
        $statement->execute();
        $statement->closeCursor();
        return $db->lastInsertId();
    }

    public function update_contract_fields($contract_id, $data) {
        $db = Database::getDB();
        $query = 'update Contract
                set Name = :Name,
                    PropertyId = :PropertyId,
                    CustomerId = :CustomerId,
                    CompanyId = :CompanyId,
                    BaseAmt = :BaseAmt,
                    CAM = :CAM,
                    BillingCycleStart = :BillingCycleStart,
                    BillingCycleEnd = :BillingCycleEnd,
                    DueDate = :DueDate,
                    LateDate = :LateDate,
                    LateFee = :LateFee,
                    StatementSendDate = :StatementSendDate,
                    NumPaymentsDue = :NumPaymentsDue,
                    TotalPaymentsDue = :TotalPaymentsDue,
                    ContractType = :ContractType,
                    TestContract = :TestContract,
                    StatementAutoReceive = :StatementAutoReceive,
                    StripePaymentMethodId = :StripePaymentMethodId
                where ContractId = :ContractId';
        $statement = $db->prepare($query);
        $statement->bindValue(':Name', $data['Name']);
        $statement->bindValue(':PropertyId', $data['PropertyId'], PDO::PARAM_INT);
        $statement->bindValue(':CustomerId', $data['CustomerId'], PDO::PARAM_INT);
        $statement->bindValue(':CompanyId', $data['CompanyId'], PDO::PARAM_INT);
        $statement->bindValue(':BaseAmt', $data['BaseAmt']);
        $statement->bindValue(':CAM', $data['CAM']);
        $statement->bindValue(':BillingCycleStart', $data['BillingCycleStart'], PDO::PARAM_INT);
        $statement->bindValue(':BillingCycleEnd', $data['BillingCycleEnd']);
        $statement->bindValue(':DueDate', $data['DueDate']);
        $statement->bindValue(':LateDate', $data['LateDate'], PDO::PARAM_INT);
        $statement->bindValue(':LateFee', $data['LateFee']);
        $statement->bindValue(':StatementSendDate', $data['StatementSendDate'], PDO::PARAM_INT);
        $statement->bindValue(':NumPaymentsDue', $data['NumPaymentsDue'], PDO::PARAM_INT);
        $statement->bindValue(':TotalPaymentsDue', $data['TotalPaymentsDue'], PDO::PARAM_INT);
        $statement->bindValue(':ContractType', $data['ContractType']);
        $statement->bindValue(':TestContract', $data['TestContract']);
        $statement->bindValue(':StatementAutoReceive', $data['StatementAutoReceive']);
        $statement->bindValue(':StripePaymentMethodId', $data['StripePaymentMethodId'], PDO::PARAM_INT);
        $statement->bindValue(':ContractId', $contract_id, PDO::PARAM_INT);
        $statement->execute();
        $statement->closeCursor();
        return true;
    }

    public function delete_contract($contract_id) {
        $db = Database::getDB();
        $query = 'delete from Contract where ContractId = :ContractId';
        $statement = $db->prepare($query);
        $statement->bindValue(':ContractId', $contract_id, PDO::PARAM_INT);
        $statement->execute();
        $statement->closeCursor();
        return true;
    }

    public function delete_multiple_contracts($contract_ids) {
        if (empty($contract_ids)) {
            return true;
        }
        $db = Database::getDB();
        $placeholders = implode(',', array_fill(0, count($contract_ids), '?'));
        $query = "delete from Contract where ContractId in ($placeholders)";
        $statement = $db->prepare($query);
        foreach ($contract_ids as $index => $id) {
            $statement->bindValue($index + 1, (int) $id, PDO::PARAM_INT);
        }
        $statement->execute();
        $statement->closeCursor();
        return true;
    }
}

?>
