<?php
class StateDB {
    public function get_all_states() {
        $db = Database::getDB();
        $query = 'SELECT StateId, Name FROM State ORDER BY StateId';
        $statement = $db->prepare($query);
        $statement->execute();
        $states = $statement->fetchAll();
        $statement->closeCursor();
        return $states;
    }
}
?>