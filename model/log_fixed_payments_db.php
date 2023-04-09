<?php
class LogFixedPaymentsDB {
    public function subtract_payment($contract_id, $completed_date, $num_payments_due, $new_payments_due) {
        $db = Database::getDB();
        $query = 'insert into LogFixedPayments (CompletedDate, CurrentPaymentsDue, ChangeAmt, NewPaymentsDue, ContractId)
                  values (:CompletedDate, :CurrentPaymentsDue, -1, :NewPaymentsDue, :ContractId)';
        $statement = $db->prepare($query);
        $statement->bindValue(':CompletedDate', $completed_date);
        $statement->bindValue(':CurrentPaymentsDue', $num_payments_due);
        $statement->bindValue(':NewPaymentsDue', $new_payments_due);
        $statement->bindValue(':ContractId', $contract_id);
        $statement->execute();
        $statement->closeCursor();
        return true;
    }
}

?>