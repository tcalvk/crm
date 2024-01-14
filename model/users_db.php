<?php 
session_start();
class UsersDB {
    public function login_user($email, $password) {
        $db = Database::getDB();
        $query = 'select * 
                 from users
                 where email = :email';
        $statement = $db->prepare($query);
        $statement->bindValue(":email", $email);
        $statement->execute();
        $row = $statement->fetch();
        $statement->closeCursor(); 
        
        $hash = $row['password'];
        $valid_password = password_verify($password, $hash);

        if ($valid_password == false) {
            return false; 
        } else {
            $_SESSION["userId"] = $row['userId'];
            $_SESSION["logged_in"] = true;
            return $row;
        }
    }
    public function check_email($email) {
        $db = Database::getDB();
        $query = 'select * 
                 from users 
                 where email = :email';
        $statement = $db->prepare($query);
        $statement->bindValue(':email', $email);
        $statement->execute();
        $row = $statement->fetch();
        $statement->closeCursor();
        if ($row == null) {
            return true;
        } else {
            return false;
        }
    }
    public function create_user($first_name, $last_name, $email, $password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $db = Database::getDB();
        $query = 'insert into users (firstname, lastname, email, password)
                 values (:firstname, :lastname, :email, :password)';
        $statement = $db->prepare($query);
        $statement->bindValue(':firstname', $first_name);
        $statement->bindValue(':lastname', $last_name);
        $statement->bindValue(':email', $email);
        $statement->bindValue(':password', $hash);
        $statement->execute();
        $statement->closeCursor();
        return true;
    }
    public function get_user_info($user_id) {
        $db = Database::getDB();
        $query = 'select * 
                 from users
                 where userId = :userId';
        $statement = $db->prepare($query);
        $statement->bindValue(':userId', $user_id);
        $statement->execute();
        $row = $statement->fetch();
        $statement->closeCursor();
        return $row;
    }

    public function change_name($user_id, $new_name) {
        $db = Database::getDB();
        $query = 'update users
                 set name = :new_name
                 where userId = :user_id';
        $statement = $db->prepare($query);
        $statement->bindValue(':new_name', $new_name);
        $statement->bindValue(':user_id', $user_id);
        $statement->execute();
        $statement->closeCursor();
        return true;
    }

    public function change_email($user_id, $new_email) {
        $db = Database::getDB(); 
        $query = 'update users
                 set email = :new_email
                 where userId = :user_id';
        $statement = $db->prepare($query);
        $statement->bindValue(':new_email', $new_email);
        $statement->bindValue(':user_id', $user_id);
        $statement->execute();
        $statement->closeCursor();
        return true;
    }

    public function check_current_password($user_id, $current_password) {
        $db = Database::getDB();
        $query = 'select *
                 from users 
                 where userId = :user_id';
        $statement = $db->prepare($query);
        $statement->bindValue(':user_id', $user_id);
        $statement->execute();
        $row = $statement->fetch();
        $statement->closeCursor();

        $hash = $row['password'];
        $valid_password = password_verify($current_password, $hash);
        if ($valid_password == false) {
            return false; 
        } else {
            return true;
        }
    }

    public function change_password($user_id, $new_password) {
        $db = Database::getDB();
        $hash = password_hash($new_password, PASSWORD_DEFAULT);
        $query = 'update users
                 set password = :password
                 where userId = :user_id';
        $statement = $db->prepare($query);
        $statement->bindValue(':password', $hash);
        $statement->bindValue(':user_id', $user_id);
        $statement->execute();
        $statement->closeCursor();
        return true;
    }

    public function get_userid_from_email($email) {
        $db = Database::getDB();
        $query = 'select * 
                 from users 
                 where email = :email';
        $statement = $db->prepare($query);
        $statement->bindValue(':email', $email);
        $statement->execute();
        $row = $statement->fetch();
        $statement->closeCursor();
        if ($row == null) {
            return false;
        } else {
            return $row;
        }
    }
    public function update_firstname ($new_firstname, $user_id) {
        $db = Database::getDB();
        $query = 'update users
                 set firstname = :new_firstname
                 where userId = :userId';
        $statement = $db->prepare($query);
        $statement->bindValue(':new_firstname', $new_firstname);
        $statement->bindValue(':userId', $user_id);
        $statement->execute();
        $statement->closeCursor();
        return true;
    }
    public function update_lastname ($new_lastname, $user_id) {
        $db = Database::getDB();
        $query = 'update users
                 set lastname = :new_lastname
                 where userId = :userId';
        $statement = $db->prepare($query);
        $statement->bindValue(':userId', $user_id);
        $statement->bindValue(':new_lastname', $new_lastname);
        $statement->execute();
        $statement->closeCursor();
        return true;
    }
}

?>