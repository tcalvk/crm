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
    public function update_statement_overdue_notification ($new_statement_overdue_notification, $user_id) {
        $db = Database::getDB();
        $query = 'update UserSettings
                 set StatementOverdueNotification = :new_statement_overdue_notification
                 where userId = :userId';
        $statement = $db->prepare($query);
        $statement->bindValue(':userId', $user_id);
        $statement->bindValue(':new_statement_overdue_notification', $new_statement_overdue_notification);
        $statement->execute();
        $statement->closeCursor();
        return true;
    }
    public function update_statement_overdue_notification_days ($new_statement_overdue_notification_days, $user_id) {
        $db = Database::getDB();
        $query = 'update UserSettings
                 set StatementOverdueNotificationDays = :new_statement_overdue_notification_days
                 where userId = :userId';
        $statement = $db->prepare($query);
        $statement->bindValue(':new_statement_overdue_notification_days', $new_statement_overdue_notification_days);
        $statement->bindValue(':userId', $user_id);
        $statement->execute();
        $statement->closeCursor();
        return true;
    }
}

?>