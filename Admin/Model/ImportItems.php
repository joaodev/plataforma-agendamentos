<?php

namespace Admin\Model;

use Core\Db\Model;
use Exception;
use PDO;

class ImportItems extends Model
{
    public function __construct()
    {
        $this->setTable('import_items');
    }

    public function getAllCategories(string $uuid): bool|array|string
    {
        try {
            $query = "
                SELECT i.item_uuid, c.deleted, 
                       c.uuid as uuid, c.name as name
                FROM import_items AS i
                    INNER JOIN categories AS c ON i.item_uuid = c.uuid
                WHERE i.parent_uuid = :parent_uuid";

            $stmt = $this->openDb()->prepare($query);
            $stmt->bindValue(":parent_uuid", $uuid);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt = null;
            $this->closeDb();
       
            return $result;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function getAllCustomers(string $uuid): bool|array|string
    {
        try {
            $query = "
                SELECT i.item_uuid, c.deleted, 
                       c.uuid as uuid, c.name as name
                FROM import_items AS i
                    INNER JOIN customers AS c ON i.item_uuid = c.uuid
                WHERE i.parent_uuid = :parent_uuid";

            $stmt = $this->openDb()->prepare($query);
            $stmt->bindValue(":parent_uuid", $uuid);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
            $stmt = null;
            $this->closeDb();

            return $result;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function getAllUsers(string $uuid): bool|array|string
    {
        try {
            $query = "
                SELECT i.item_uuid, u.deleted, 
                       u.uuid as uuid, u.name as name
                FROM import_items AS i
                    INNER JOIN user AS u ON i.item_uuid = u.uuid
                WHERE i.parent_uuid = :parent_uuid";

            $stmt = $this->openDb()->prepare($query);
            $stmt->bindValue(":parent_uuid", $uuid);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
            $stmt = null;
            $this->closeDb();

            return $result;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function getAllProperties(string $uuid): bool|array|string
    {
        try {
            $query = "
                SELECT i.item_uuid, p.deleted,
                       p.uuid as uuid, p.title as name
                FROM import_items AS i
                    INNER JOIN properties AS p ON i.item_uuid = p.uuid
                WHERE i.parent_uuid = :parent_uuid";

            $stmt = $this->openDb()->prepare($query);
            $stmt->bindValue(":parent_uuid", $uuid);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
            $stmt = null;
            $this->closeDb();

            return $result;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}