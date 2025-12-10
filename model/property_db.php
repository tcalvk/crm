<?php
class PropertyDB {
    public function get_properties($user_id) {
        $db = Database::getDB();
        $query = 'select p.*, s.Name as StateName, u.email as OwnerEmail
                  from Property p
                  left join State s on p.StateId = s.StateId
                  left join users u on p.OwnedBy = u.userId
                  where p.OwnedBy = :OwnedBy
                  order by p.Name';
        $statement = $db->prepare($query);
        $statement->bindValue(':OwnedBy', $user_id, PDO::PARAM_INT);
        $statement->execute();
        $properties = $statement->fetchAll();
        $statement->closeCursor();
        return $properties;
    }

    public function get_properties_sudo() {
        $db = Database::getDB();
        $query = 'select p.*, s.Name as StateName, u.email as OwnerEmail
                  from Property p
                  left join State s on p.StateId = s.StateId
                  left join users u on p.OwnedBy = u.userId
                  order by p.Name';
        $statement = $db->prepare($query);
        $statement->execute();
        $properties = $statement->fetchAll();
        $statement->closeCursor();
        return $properties;
    }

    public function get_property($property_id) {
        $db = Database::getDB();
        $query = 'select p.*, s.Name as StateName, u.email as OwnerEmail
                  from Property p
                  left join State s on p.StateId = s.StateId
                  left join users u on p.OwnedBy = u.userId
                  where p.PropertyId = :PropertyId';
        $statement = $db->prepare($query);
        $statement->bindValue(':PropertyId', $property_id, PDO::PARAM_INT);
        $statement->execute();
        $property = $statement->fetch();
        $statement->closeCursor();
        return $property;
    }

    public function create_property($data) {
        $db = Database::getDB();
        $query = 'insert into Property (Name, Address1, Address2, Address3, City, StateId, Zip, OwnedBy)
                  values (:Name, :Address1, :Address2, :Address3, :City, :StateId, :Zip, :OwnedBy)';
        $statement = $db->prepare($query);
        $statement->bindValue(':Name', $data['Name']);
        $statement->bindValue(':Address1', $data['Address1']);
        $statement->bindValue(':Address2', $data['Address2']);
        $statement->bindValue(':Address3', $data['Address3']);
        $statement->bindValue(':City', $data['City']);
        $statement->bindValue(':StateId', $data['StateId']);
        $statement->bindValue(':Zip', $data['Zip']);
        $statement->bindValue(':OwnedBy', $data['OwnedBy'], PDO::PARAM_INT);
        $statement->execute();
        $statement->closeCursor();
        return $db->lastInsertId();
    }

    public function delete_property($property_id) {
        $db = Database::getDB();
        $query = 'delete from Property where PropertyId = :PropertyId';
        $statement = $db->prepare($query);
        $statement->bindValue(':PropertyId', $property_id, PDO::PARAM_INT);
        $statement->execute();
        $statement->closeCursor();
        return true;
    }

    public function delete_multiple_properties($property_ids) {
        if (empty($property_ids)) {
            return true;
        }
        $db = Database::getDB();
        $placeholders = implode(',', array_fill(0, count($property_ids), '?'));
        $query = "delete from Property where PropertyId in ($placeholders)";
        $statement = $db->prepare($query);
        foreach ($property_ids as $index => $id) {
            $statement->bindValue($index + 1, (int) $id, PDO::PARAM_INT);
        }
        $statement->execute();
        $statement->closeCursor();
        return true;
    }
}
?>
