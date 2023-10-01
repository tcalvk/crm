<?php 
class UserSettingsDB {
    public function get_user_settings ($user_id) {
        $db = Database::getDB();
        $query = 'select us.*
                 from UserSettings us
                 where us.userId = :userId';
        $statement = $db->prepare($query);
        $statement->bindValue(':userId', $user_id);
        $statement->execute();
        $row = $statement->fetch();
        $statement->closeCursor();
        return $row;
    }
}

?>