<?php
class FilesDB {
    private function get_primary_key_column() {
        $db = Database::getDB();
        $statement = $db->prepare('describe Files');
        $statement->execute();
        $columns = $statement->fetchAll();
        $statement->closeCursor();

        $ordered_candidates = ['Field', 'FileId', 'FilesId', 'Id', 'ID', 'file_id'];
        foreach ($ordered_candidates as $candidate) {
            foreach ($columns as $column) {
                if (isset($column['Field']) && strcasecmp($column['Field'], $candidate) === 0) {
                    return $column['Field'];
                }
            }
        }

        if (!empty($columns) && isset($columns[0]['Field'])) {
            return $columns[0]['Field'];
        }

        return 'Field';
    }

    private function quote_identifier($identifier) {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }

    public function create_file($provider, $bucket, $object_name, $file_name, $content_type, $size_bytes, $created_by) {
        $db = Database::getDB();
        $query = 'insert into Files
                    (Provider, Bucket, ObjectName, FileName, ContentType, SizeBytes, CreatedBy)
                  values
                    (:Provider, :Bucket, :ObjectName, :FileName, :ContentType, :SizeBytes, :CreatedBy)';
        $statement = $db->prepare($query);
        $statement->bindValue(':Provider', $provider);
        $statement->bindValue(':Bucket', $bucket);
        $statement->bindValue(':ObjectName', $object_name);
        $statement->bindValue(':FileName', $file_name);
        $statement->bindValue(':ContentType', $content_type);
        $statement->bindValue(':SizeBytes', $size_bytes, PDO::PARAM_INT);
        $statement->bindValue(':CreatedBy', $created_by, PDO::PARAM_INT);
        $statement->execute();
        $statement->closeCursor();
        return (int) $db->lastInsertId();
    }

    public function get_file($file_id) {
        $db = Database::getDB();
        $pk = $this->quote_identifier($this->get_primary_key_column());
        $query = 'select f.*, f.' . $pk . ' as FileId, u.firstname, u.lastname, u.email
                  from Files f
                  left join users u on f.CreatedBy = u.userId
                  where f.' . $pk . ' = :FileId
                    and (f.Deleted is null or f.Deleted = 0)';
        $statement = $db->prepare($query);
        $statement->bindValue(':FileId', $file_id, PDO::PARAM_INT);
        $statement->execute();
        $row = $statement->fetch();
        $statement->closeCursor();
        return $row;
    }

    public function get_file_for_user($file_id, $user_id) {
        $db = Database::getDB();
        $pk = $this->quote_identifier($this->get_primary_key_column());
        $query = 'select f.*, f.' . $pk . ' as FileId, u.firstname, u.lastname, u.email
                  from Files f
                  left join users u on f.CreatedBy = u.userId
                  where f.' . $pk . ' = :FileId
                    and f.CreatedBy = :CreatedBy
                    and (f.Deleted is null or f.Deleted = 0)';
        $statement = $db->prepare($query);
        $statement->bindValue(':FileId', $file_id, PDO::PARAM_INT);
        $statement->bindValue(':CreatedBy', $user_id, PDO::PARAM_INT);
        $statement->execute();
        $row = $statement->fetch();
        $statement->closeCursor();
        return $row;
    }

    public function get_files_for_user($user_id) {
        $db = Database::getDB();
        $pk = $this->quote_identifier($this->get_primary_key_column());
        $query = 'select f.*, f.' . $pk . ' as FileId, u.firstname, u.lastname, u.email
                  from Files f
                  left join users u on f.CreatedBy = u.userId
                  where f.CreatedBy = :CreatedBy
                    and (f.Deleted is null or f.Deleted = 0)
                  order by f.CreatedAt desc';
        $statement = $db->prepare($query);
        $statement->bindValue(':CreatedBy', $user_id, PDO::PARAM_INT);
        $statement->execute();
        $rows = $statement->fetchAll();
        $statement->closeCursor();
        return $rows;
    }

    public function get_files_all($created_by = null) {
        $db = Database::getDB();
        $pk = $this->quote_identifier($this->get_primary_key_column());
        $query = 'select f.*, f.' . $pk . ' as FileId, u.firstname, u.lastname, u.email
                  from Files f
                  left join users u on f.CreatedBy = u.userId
                  where (f.Deleted is null or f.Deleted = 0)';
        if ($created_by !== null) {
            $query .= ' and f.CreatedBy = :CreatedBy';
        }
        $query .= ' order by f.CreatedAt desc';
        $statement = $db->prepare($query);
        if ($created_by !== null) {
            $statement->bindValue(':CreatedBy', $created_by, PDO::PARAM_INT);
        }
        $statement->execute();
        $rows = $statement->fetchAll();
        $statement->closeCursor();
        return $rows;
    }

    public function get_upload_users() {
        $db = Database::getDB();
        $query = 'select distinct u.userId, u.firstname, u.lastname, u.email
                  from Files f
                  inner join users u on f.CreatedBy = u.userId
                  where (f.Deleted is null or f.Deleted = 0)
                  order by u.firstname asc, u.lastname asc, u.email asc';
        $statement = $db->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll();
        $statement->closeCursor();
        return $rows;
    }

    public function delete_file($file_id) {
        $db = Database::getDB();
        $pk = $this->quote_identifier($this->get_primary_key_column());
        $query = 'update Files
                  set Deleted = 1
                  where ' . $pk . ' = :FileId
                    and (Deleted is null or Deleted = 0)';
        $statement = $db->prepare($query);
        $statement->bindValue(':FileId', $file_id, PDO::PARAM_INT);
        $statement->execute();
        $affected = $statement->rowCount();
        $statement->closeCursor();
        return $affected > 0;
    }

    public function delete_file_for_user($file_id, $user_id) {
        $db = Database::getDB();
        $pk = $this->quote_identifier($this->get_primary_key_column());
        $query = 'update Files
                  set Deleted = 1
                  where ' . $pk . ' = :FileId
                    and CreatedBy = :CreatedBy
                    and (Deleted is null or Deleted = 0)';
        $statement = $db->prepare($query);
        $statement->bindValue(':FileId', $file_id, PDO::PARAM_INT);
        $statement->bindValue(':CreatedBy', $user_id, PDO::PARAM_INT);
        $statement->execute();
        $affected = $statement->rowCount();
        $statement->closeCursor();
        return $affected > 0;
    }
}
?>
