<?php 
class ContractTermDB {
    public function get_current_term($contract_id) {
        $db = Database::getDB();
        $query = 'select case when ct.BaseAmt is null then "na" else ct.BaseAmt end "BaseAmt"
        from Contract c 
        left join ContractTerm ct on c.ContractId = ct.ContractId
        where ((CURRENT_DATE >= ct.TermStartDate and CURRENT_DATE <= ct.TermEndDate) or ct.TermStartDate is null)
        and c.ContractId = :ContractId';
        $statement = $db->prepare($query);
        $statement->bindValue(':ContractId', $contract_id);
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
        where c.ContractId = :ContractId';
        $statement = $db->prepare($query);
        $statement->bindValue(':ContractId', $contract_id);
        $statement->execute();
        $contract_terms = $statement->fetchAll();
        $statement->closeCursor();
        return $contract_terms;
    }
}

?>