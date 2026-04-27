<?php 
session_start();

class UserInvitesDB {
    public function invite_code_exists($invite_code) {
        $db = Database::getDB();
        $query = 'select count(*) as count from UserInvites where InviteCode = :inviteCode';
        $statement = $db->prepare($query);
        $statement->bindValue(':inviteCode', $invite_code);
        $statement->execute();
        $row = $statement->fetch();
        $statement->closeCursor();
        return isset($row['count']) && $row['count'] > 0;
    }

    public function create_invite($email, $invite_code, $status = 'Pending') {
        $db = Database::getDB();
        $query = 'insert into UserInvites (Email, InviteCode, Status)
                 values (:email, :inviteCode, :status)';
        $statement = $db->prepare($query);
        $statement->bindValue(':email', $email);
        $statement->bindValue(':inviteCode', $invite_code);
        $statement->bindValue(':status', $status);
        $statement->execute();
        $statement->closeCursor();
        return true;
    }

    public function get_invites_paginated($limit, $offset) {
        $db = Database::getDB();
        $query = 'select * from UserInvites order by CreatedAt desc limit :limit offset :offset';
        $statement = $db->prepare($query);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();
        $rows = $statement->fetchAll();
        $statement->closeCursor();
        return $rows;
    }

    public function get_invite_count() {
        $db = Database::getDB();
        $query = 'select count(*) as count from UserInvites';
        $statement = $db->prepare($query);
        $statement->execute();
        $row = $statement->fetch();
        $statement->closeCursor();
        return isset($row['count']) ? (int)$row['count'] : 0;
    }

    public function get_invites_paginated_search($limit, $offset, $search) {
        $db = Database::getDB();
        $query = 'select * from UserInvites 
                  where Email like :search
                     or InviteCode like :search
                     or Status like :search
                  order by CreatedAt desc
                  limit :limit offset :offset';
        $statement = $db->prepare($query);
        $statement->bindValue(':search', '%' . $search . '%');
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();
        $rows = $statement->fetchAll();
        $statement->closeCursor();
        return $rows;
    }

    public function get_invite_count_search($search) {
        $db = Database::getDB();
        $query = 'select count(*) as count from UserInvites 
                  where Email like :search
                     or InviteCode like :search
                     or Status like :search';
        $statement = $db->prepare($query);
        $statement->bindValue(':search', '%' . $search . '%');
        $statement->execute();
        $row = $statement->fetch();
        $statement->closeCursor();
        return isset($row['count']) ? (int)$row['count'] : 0;
    }

    public function get_pending_invite_by_code($invite_code) {
        $db = Database::getDB();
        $query = 'select * from UserInvites where InviteCode = :inviteCode and Status = "Pending"';
        $statement = $db->prepare($query);
        $statement->bindValue(':inviteCode', $invite_code);
        $statement->execute();
        $row = $statement->fetch();
        $statement->closeCursor();
        return $row;
    }

    public function mark_invite_accepted($invite_code) {
        $db = Database::getDB();
        $query = 'update UserInvites set Status = "Accepted" where InviteCode = :inviteCode';
        $statement = $db->prepare($query);
        $statement->bindValue(':inviteCode', $invite_code);
        $statement->execute();
        $statement->closeCursor();
        return true;
    }
}

?>
