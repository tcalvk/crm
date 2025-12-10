<?php 
class ContractTermDB {
    public function get_current_term($contract_id) {
        $db = Database::getDB();
        $query = 'select 
                    ct.TermStartDate,
                    ct.TermEndDate,
                    case when ct.BaseAmt is null then "na" else ct.BaseAmt end "BaseAmt"
                  from ContractTerm ct
                  where ct.ContractId = :ContractId
                    and ((CURRENT_DATE >= ct.TermStartDate and CURRENT_DATE <= ct.TermEndDate) or ct.TermStartDate is null)
                  order by ct.TermStartDate desc
                  limit 1';
        $statement = $db->prepare($query);
        $statement->bindValue(':ContractId', $contract_id, PDO::PARAM_INT);
        $statement->execute();
        $current_term = $statement->fetch();
        $statement->closeCursor();
        return $current_term;
    }
    public function get_all_terms ($contract_id) {
        $db = Database::getDB();
        $query = 'select ct.* 
        from Contract c 
        right join ContractTerm ct on c.ContractId = ct.ContractId
        where c.ContractId = :ContractId
        order by ct.TermStartDate';
        $statement = $db->prepare($query);
        $statement->bindValue(':ContractId', $contract_id);
        $statement->execute();
        $contract_terms = $statement->fetchAll();
        $statement->closeCursor();
        return $contract_terms;
    }

    public function get_term($contract_term_id) {
        $db = Database::getDB();
        $query = 'select * from ContractTerm where ContractTermId = :ContractTermId';
        $statement = $db->prepare($query);
        $statement->bindValue(':ContractTermId', $contract_term_id, PDO::PARAM_INT);
        $statement->execute();
        $contract_term = $statement->fetch();
        $statement->closeCursor();
        return $contract_term;
    }

    public function create_term($contract_id, $term_start_date, $term_end_date, $base_amt) {
        $db = Database::getDB();
        $query = 'insert into ContractTerm (ContractId, TermStartDate, TermEndDate, BaseAmt)
                  values (:ContractId, :TermStartDate, :TermEndDate, :BaseAmt)';
        $statement = $db->prepare($query);
        $statement->bindValue(':ContractId', $contract_id, PDO::PARAM_INT);
        $statement->bindValue(':TermStartDate', $term_start_date);
        $statement->bindValue(':TermEndDate', $term_end_date);
        $statement->bindValue(':BaseAmt', $base_amt);
        $statement->execute();
        $statement->closeCursor();
        return $db->lastInsertId();
    }

    public function delete_term($contract_term_id, $contract_id) {
        $db = Database::getDB();
        $query = 'delete from ContractTerm
                  where ContractTermId = :ContractTermId
                    and ContractId = :ContractId';
        $statement = $db->prepare($query);
        $statement->bindValue(':ContractTermId', $contract_term_id, PDO::PARAM_INT);
        $statement->bindValue(':ContractId', $contract_id, PDO::PARAM_INT);
        $statement->execute();
        $statement->closeCursor();
        return true;
    }
}

?>
