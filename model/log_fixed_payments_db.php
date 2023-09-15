<?php
class LogFixedPaymentsDB {
    public function subtract_payment($contract_id, $completed_date, $num_payments_due, $new_payments_due, $statement_number) {
        $db = Database::getDB();
        $query = 'insert into LogFixedPayments (CompletedDate, CurrentPaymentsDue, ChangeAmt, NewPaymentsDue, ContractId, StatementNumber)
                  values (:CompletedDate, :CurrentPaymentsDue, -1, :NewPaymentsDue, :ContractId, :StatementNumber)';
        $statement = $db->prepare($query);
        $statement->bindValue(':CompletedDate', $completed_date);
        $statement->bindValue(':CurrentPaymentsDue', $num_payments_due);
        $statement->bindValue(':NewPaymentsDue', $new_payments_due);
        $statement->bindValue(':ContractId', $contract_id);
        $statement->bindValue(':StatementNumber', $statement_number);
        $statement->execute();
        $statement->closeCursor();
        return true;
    }
    public function clear_paid_date($contract_id, $completed_date, $num_payments_due, $new_payments_due, $statement_number) {
        $db = Database::getDB();
        $query = 'insert into LogFixedPayments (CompletedDate, CurrentPaymentsDue, ChangeAmt, NewPaymentsDue, ContractId, StatementNumber)
        values (:CompletedDate, :CurrentPaymentsDue, 1, :NewPaymentsDue, :ContractId, :StatementNumber)';
        $statement = $db->prepare($query);
        $statement->bindValue(':CompletedDate', $completed_date);
        $statement->bindValue(':CurrentPaymentsDue', $num_payments_due);
        $statement->bindValue(':NewPaymentsDue', $new_payments_due);
        $statement->bindValue(':ContractId', $contract_id);
        $statement->bindValue(':StatementNumber', $statement_number);
        $statement->execute();
        $statement->closeCursor();
        return true;
    }
}

?>