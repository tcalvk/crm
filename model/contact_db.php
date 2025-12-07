<?php 
class ContactDB {
    public function get_contacts($contract_id) {
        $db = Database::getDB();
        $query =  'select c.* 
                  from Contact c
                  left join Customer cu on c.CustomerId = cu.CustomerId
                  left join Contract ct on cu.CustomerId = ct.CustomerId 
                  where ct.ContractId = :ContractId
                  and c.ReceiveStatements = 1';
        $statement = $db->prepare($query);
        $statement->bindValue(':ContractId', $contract_id);
        $statement->execute();
        $contacts = $statement->fetchAll();
        $statement->closeCursor(); 
        return $contacts;
    }

    public function get_contacts_by_customer($customer_id, $limit = null) {
        $db = Database::getDB();
        $query = 'select *
                  from Contact
                  where CustomerId = :CustomerId
                  order by LastName, FirstName';
        if ($limit !== null) {
            $query .= ' limit :limit';
        }
        $statement = $db->prepare($query);
        $statement->bindValue(':CustomerId', $customer_id, PDO::PARAM_INT);
        if ($limit !== null) {
            $statement->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        }
        $statement->execute();
        $contacts = $statement->fetchAll();
        $statement->closeCursor();
        return $contacts;
    }

    public function get_contact($contact_id) {
        $db = Database::getDB();
        $query = 'select *
                  from Contact
                  where ContactId = :ContactId';
        $statement = $db->prepare($query);
        $statement->bindValue(':ContactId', $contact_id, PDO::PARAM_INT);
        $statement->execute();
        $contact = $statement->fetch();
        $statement->closeCursor();
        return $contact;
    }

    public function create_contact($data) {
        $db = Database::getDB();
        $query = 'insert into Contact
                    (FirstName, LastName, Address1, Address2, City, StateId, Zip, Phone, Email, ReceiveStatements, CustomerId)
                  values
                    (:FirstName, :LastName, :Address1, :Address2, :City, :StateId, :Zip, :Phone, :Email, :ReceiveStatements, :CustomerId)';
        $statement = $db->prepare($query);
        $statement->bindValue(':FirstName', $data['FirstName']);
        $statement->bindValue(':LastName', $data['LastName']);
        $statement->bindValue(':Address1', $data['Address1']);
        $statement->bindValue(':Address2', $data['Address2']);
        $statement->bindValue(':City', $data['City']);
        $statement->bindValue(':StateId', $data['StateId']);
        $statement->bindValue(':Zip', $data['Zip']);
        $statement->bindValue(':Phone', $data['Phone']);
        $statement->bindValue(':Email', $data['Email']);
        $statement->bindValue(':ReceiveStatements', (int) $data['ReceiveStatements'], PDO::PARAM_INT);
        $statement->bindValue(':CustomerId', $data['CustomerId'], PDO::PARAM_INT);
        $statement->execute();
        $statement->closeCursor();
        return $db->lastInsertId();
    }

    public function update_contact($contact_id, $data) {
        $db = Database::getDB();
        $query = 'update Contact
                  set FirstName = :FirstName,
                      LastName = :LastName,
                      Address1 = :Address1,
                      Address2 = :Address2,
                      City = :City,
                      StateId = :StateId,
                      Zip = :Zip,
                      Phone = :Phone,
                      Email = :Email,
                      ReceiveStatements = :ReceiveStatements
                  where ContactId = :ContactId';
        $statement = $db->prepare($query);
        $statement->bindValue(':FirstName', $data['FirstName']);
        $statement->bindValue(':LastName', $data['LastName']);
        $statement->bindValue(':Address1', $data['Address1']);
        $statement->bindValue(':Address2', $data['Address2']);
        $statement->bindValue(':City', $data['City']);
        $statement->bindValue(':StateId', $data['StateId']);
        $statement->bindValue(':Zip', $data['Zip']);
        $statement->bindValue(':Phone', $data['Phone']);
        $statement->bindValue(':Email', $data['Email']);
        $statement->bindValue(':ReceiveStatements', (int) $data['ReceiveStatements'], PDO::PARAM_INT);
        $statement->bindValue(':ContactId', $contact_id, PDO::PARAM_INT);
        $statement->execute();
        $statement->closeCursor();
        return true;
    }

    public function delete_contact($contact_id) {
        $db = Database::getDB();
        $query = 'delete from Contact
                  where ContactId = :ContactId';
        $statement = $db->prepare($query);
        $statement->bindValue(':ContactId', $contact_id, PDO::PARAM_INT);
        $statement->execute();
        $statement->closeCursor();
        return true;
    }

    public function delete_multiple_contacts($contact_ids) {
        if (empty($contact_ids)) {
            return true;
        }
        $db = Database::getDB();
        $placeholders = implode(',', array_fill(0, count($contact_ids), '?'));
        $query = "delete from Contact where ContactId in ($placeholders)";
        $statement = $db->prepare($query);
        foreach ($contact_ids as $index => $id) {
            $statement->bindValue($index + 1, (int) $id, PDO::PARAM_INT);
        }
        $statement->execute();
        $statement->closeCursor();
        return true;
    }
}

?>
