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
}

?>