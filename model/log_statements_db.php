<?php 
class LogStatementsDB {
    public function log_fixed_statement($invoice_number, $completed_date, $total, $payment_number, $contract_id, $due_date, $base_amt, $cam) {
        $db = Database::getDB();
        $query = 'insert into LogStatements (StatementNumber, CreatedDate, TotalAmt, PaymentNumber, ContractId, DueDate, BaseAmt, CAM)
                 values (:StatementNumber, :CreatedDate, :TotalAmt, :PaymentNumber, :ContractId, :DueDate, :BaseAmt, :CAM)';
        $statement = $db->prepare($query);
        $statement->bindValue(':StatementNumber', $invoice_number);
        $statement->bindValue(':CreatedDate', $completed_date);
        $statement->bindValue(':TotalAmt', $total);
        $statement->bindValue(':PaymentNumber', $payment_number);
        $statement->bindValue(':ContractId', $contract_id);
        $statement->bindValue(':DueDate', $due_date);
        $statement->bindValue(':BaseAmt', $base_amt);
        $statement->bindValue(':CAM', $cam);
        $statement->execute();
        $statement->closeCursor();
        return true;
    }
    public function log_evergreen_statement($invoice_number, $completed_date, $total, $contract_id, $due_date, $base_amt, $cam) {
        $db = Database::getDB();
        $query = 'insert into LogStatements (StatementNumber, CreatedDate, TotalAmt, ContractId, DueDate, BaseAmt, CAM)
                 values (:StatementNumber, :CreatedDate, :TotalAmt, :ContractId, :DueDate, :BaseAmt, :CAM)';
        $statement = $db->prepare($query);
        $statement->bindValue(':StatementNumber', $invoice_number);
        $statement->bindValue(':CreatedDate', $completed_date);
        $statement->bindValue(':TotalAmt', $total);
        $statement->bindValue(':ContractId', $contract_id);
        $statement->bindValue(':DueDate', $due_date);
        $statement->bindValue(':BaseAmt', $base_amt);
        $statement->bindValue(':CAM', $cam);
        $statement->execute();
        $statement->closeCursor();
        return true;
    }
    public function get_statements_limit3($customer_id) {
        $db = Database::getDB();
        $query = 'select ls.StatementNumber, cast(ls.CreatedDate as date) "CreatedDate", cast(ls.PaidDate as date) "PaidDate", ls.TotalAmt, ls.PaymentNumber, p.Name "PropertyName", p.Address1
        from LogStatements ls 
        left join Contract c on ls.ContractId = c.ContractId
        left join Customer cu on c.CustomerId = cu.CustomerId
        left join Property p on c.PropertyId = p.PropertyId
        where cu.CustomerId = :CustomerId
        order by ls.CreatedDate desc 
        limit 3';
        $statement = $db->prepare($query);
        $statement->bindValue(':CustomerId', $customer_id);
        $statement->execute();
        $statements = $statement->fetchAll();
        $statement->closeCursor();
        return $statements;
    }
    public function get_statement($statement_number) {
        $db = Database::getDB();
        $query = 'select ls.StatementNumber, cast(ls.CreatedDate as date) "CreatedDate", cast(ls.PaidDate as date) "PaidDate", ls.TotalAmt, ls.PaymentNumber,
        cu.Name "CustomerName", cu.CustomerId, ls.WrittenOff, ls.PaymentAmount, ls.DueDate, c.StatementAutoReceive, ls.BaseAmt, ls.CAM, c.Name "ContractName",
        c.ContractId,
        case 
            when p.Name is not null then p.Name 
            else p.Address1
        end "Property",
        case
            when ls.PaidDate is null then "Unpaid"
            when ls.PaymentAmount < ls.TotalAmt then "Partial Payment"
            else "Paid"
        end "Status"
        from LogStatements ls 
        left join Contract c on ls.ContractId = c.ContractId
        left join Customer cu on c.CustomerId = cu.CustomerId
        left join Property p on c.PropertyId = p.PropertyId
        where ls.StatementNumber = :StatementNumber';
        $statement = $db->prepare($query);
        $statement->bindValue(':StatementNumber', $statement_number);
        $statement->execute();
        $statement_data = $statement->fetch();
        $statement->closeCursor();
        return $statement_data;
    }
    public function get_statements($customer_id) {
        $db = Database::getDB();
        $query = 'select ls.StatementNumber, cast(ls.CreatedDate as date) "CreatedDate", cast(ls.PaidDate as date) "PaidDate", ls.TotalAmt, ls.PaymentNumber, p.Name "PropertyName", p.Address1
        from LogStatements ls 
        left join Contract c on ls.ContractId = c.ContractId
        left join Customer cu on c.CustomerId = cu.CustomerId
        left join Property p on c.PropertyId = p.PropertyId
        where cu.CustomerId = :CustomerId
        order by ls.CreatedDate desc';
        $statement = $db->prepare($query);
        $statement->bindValue(':CustomerId', $customer_id);
        $statement->execute();
        $statements = $statement->fetchAll();
        $statement->closeCursor();
        return $statements;
    }
    public function mark_as_paid($statement_number, $paid_date, $payment_amount) {
        $db = Database::getDB();
        $query = 'update LogStatements
                  set 
                  PaidDate = :PaidDate,
                  PaymentAmount = :PaymentAmount
                  where StatementNumber = :StatementNumber';
        $statement = $db->prepare($query);
        $statement->bindValue(':PaidDate', $paid_date);
        $statement->bindValue(':StatementNumber', $statement_number);
        $statement->bindValue(':PaymentAmount', $payment_amount);
        $statement->execute();
        $statement->closeCursor();
        return true;
    }
    public function clear_paid_date($statement_number) {
        $db = Database::getDB();
        $query = 'update LogStatements
                 set 
                 PaidDate = null, 
                 PaymentAmount = null
                 where StatementNumber = :StatementNumber';
        $statement = $db->prepare($query);
        $statement->bindValue(':StatementNumber', $statement_number);
        $statement->execute();
        $statement->closeCursor();
        return true;
    }
    public function write_off_statement($statement_number) {
        $db = Database::getDB();
        $query = 'update LogStatements
                 set WrittenOff = 1
                 where StatementNumber = :StatementNumber';
        $statement = $db->prepare($query);
        $statement->bindValue(':StatementNumber', $statement_number);
        $statement->execute();
        $statement->closeCursor();
        return true;
    }
    public function get_contract_info($statement_number) {
        $db = Database::getDB();
        $query = 'select ls.StatementNumber, c.ContractId, c.NumPaymentsDue, c.ContractType
                 from LogStatements ls
                 left join Contract c on ls.ContractId = c.ContractId
                 where ls.StatementNumber = :StatementNumber';
        $statement = $db->prepare($query);
        $statement->bindValue(':StatementNumber', $statement_number);
        $statement->execute();
        $row = $statement->fetch();
        $statement->closeCursor();
        return $row;
    }
    public function generate_statement_number() {
        $count = 1;
        while ($count > 0) {
            $invoice_number = mt_rand(100000, 999999);
            $db = Database::getDB();
            $query = 'select count(ls.StatementNumber) "RowCount"
                    from LogStatements ls
                    where ls.StatementNumber = :StatementNumber';
            $statement = $db->prepare($query);
            $statement->bindValue(':StatementNumber', $invoice_number);
            $statement->execute();
            $row_count = $statement->fetch();
            $statement->closeCursor();

            $count = $row_count['RowCount'];
        }
        return $invoice_number;
    }
    public function get_overdue_statements() {
        $db = Database::getDB();
        $query = 'select ls.StatementNumber, u.firstname "UserFirstName", cu.Name "CustomerName", c.Name "ContractName", ls.DueDate, ls.CreatedDate, u.email
        from LogStatements ls 
        left join Contract c on ls.ContractId = c.ContractId 
        left join Customer cu on c.CustomerId = cu.CustomerId 
        left join users u on cu.userId = u.userId
        left join UserSettings us on u.userId = us.userId
        where us.StatementOverdueNotification = "true" 
        and curdate() > date_add(ls.DueDate, interval us.StatementOverdueNotificationDays day)
        and ls.PaidDate is null
        and (c.TestContract is null or c.TestContract = 0)
        and ls.OverdueStatementNotificationSent = 0';
        //and c.TestContract = 1';
        $statement = $db->prepare($query);
        $statement->execute();
        $overdue_statements = $statement->fetchAll();
        $statement->closeCursor();
        return $overdue_statements; 
    }
    public function get_overdue_statements_test() {
        $db = Database::getDB();
        $query = 'select ls.StatementNumber, u.firstname "UserFirstName", cu.Name "CustomerName", c.Name "ContractName", ls.DueDate, ls.CreatedDate, u.email
        from LogStatements ls 
        left join Contract c on ls.ContractId = c.ContractId 
        left join Customer cu on c.CustomerId = cu.CustomerId 
        left join users u on cu.userId = u.userId
        left join UserSettings us on u.userId = us.userId
        where us.StatementOverdueNotification = "true" 
        and curdate() > date_add(ls.DueDate, interval us.StatementOverdueNotificationDays day)
        and ls.PaidDate is null
        and c.TestContract = 1
        and ls.OverdueStatementNotificationSent = 0';
        $statement = $db->prepare($query);
        $statement->execute();
        $overdue_statements = $statement->fetchAll();
        $statement->closeCursor();
        return $overdue_statements; 
    }
    public function update_overdue_statements($statement_number) {
        $db = Database::getDB();
        $query = 'update LogStatements
                 set OverdueStatementNotificationSent = 1
                 where StatementNumber = :StatementNumber';
        $statement = $db->prepare($query);
        $statement->bindValue(':StatementNumber', $statement_number);
        $statement->execute();
        $statement->closeCursor();
        return true;
    }
    public function get_statements_due_autoreceive() {
        $db = Database::getDB();
        $query = 'select ls.StatementNumber, c.TestContract, c.StatementAutoReceive, ls.TotalAmt, c.ContractType, c.NumPaymentsDue, c.ContractId, u.email, c.Name "ContractName", ct.Name "CustomerName", cast(ls.CreatedDate as date) "CreatedDate"
        from LogStatements ls 
        left join Contract c on ls.ContractId = c.ContractId
        left join Customer ct on c.CustomerId = ct.CustomerId 
        left join users u on ct.userId = u.userId
        where c.StatementAutoReceive = "true"
        and ls.DueDate = current_date()
        and ls.PaidDate is null'; 
        $statement = $db->prepare($query);
        $statement->execute();
        $statements = $statement->fetchAll();
        $statement->closeCursor();
        return $statements;
    }
}

?>