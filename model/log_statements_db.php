<?php 
class LogStatementsDB {
    public function log_fixed_statement($invoice_number, $completed_date, $total, $payment_number, $contract_id) {
        $db = Database::getDB();
        $query = 'insert into LogStatements (StatementNumber, CreatedDate, PaidDate, TotalAmt, PaymentNumber, ContractId)
                 values (:StatementNumber, :CreatedDate, :PaidDate, :TotalAmt, :PaymentNumber, :ContractId)';
        $statement = $db->prepare($query);
        $statement->bindValue(':StatementNumber', $invoice_number);
        $statement->bindValue(':CreatedDate', $completed_date);
        $statement->bindValue(':PaidDate', $completed_date);
        $statement->bindValue(':TotalAmt', $total);
        $statement->bindValue(':PaymentNumber', $payment_number);
        $statement->bindValue(':ContractId', $contract_id);
        $statement->execute();
        $statement->closeCursor();
        return true;
    }
    public function log_evergreen_statement($invoice_number, $completed_date, $total, $contract_id) {
        $db = Database::getDB();
        $query = 'insert into LogStatements (StatementNumber, CreatedDate, PaidDate, TotalAmt, ContractId)
                 values (:StatementNumber, :CreatedDate, :PaidDate, :TotalAmt, :ContractId)';
        $statement = $db->prepare($query);
        $statement->bindValue(':StatementNumber', $invoice_number);
        $statement->bindValue(':CreatedDate', $completed_date);
        $statement->bindValue(':PaidDate', $completed_date);
        $statement->bindValue(':TotalAmt', $total);
        $statement->bindValue(':ContractId', $contract_id);
        $statement->execute();
        $statement->closeCursor();
        return true;
    }
    public function get_statements_limit3($customer_id) {
        $db = Database::getDB();
        $query = 'select ls.StatementNumber, cast(ls.CreatedDate as date) "CreatedDate", cast(ls.PaidDate as date) "PaidDate", ls.TotalAmt, ls.PaymentNumber
        from LogStatements ls 
        left join Contract c on ls.ContractId = c.ContractId
        left join Customer cu on c.CustomerId = cu.CustomerId
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
        cu.Name "CustomerName"
        from LogStatements ls 
        left join Contract c on ls.ContractId = c.ContractId
        left join Customer cu on c.CustomerId = cu.CustomerId
        where ls.StatementNumber = :StatementNumber';
        $statement = $db->prepare($query);
        $statement->bindValue(':StatementNumber', $statement_number);
        $statement->execute();
        $statement_data = $statement->fetch();
        $statement->closeCursor();
        return $statement_data;
    }
}

?>