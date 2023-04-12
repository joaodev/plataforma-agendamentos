<?php

namespace Admin\Model;

use Core\Db\Model;
use Exception;
use PDO;

class Subcategories extends Model
{
    public function __construct()
    {
        $this->setTable('subcategories');
    }

    public function getOne($uuid)
    {
        try {
            $query = "
                SELECT uuid, category_uuid, name, slug, status, created_at, updated_at
                FROM subcategories
                WHERE uuid = :uuid
            ";

            $stmt = $this->openDb()->prepare($query);
            $stmt->bindValue(":uuid", $uuid);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = null;
            $this->closeDb();

            return $result;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function getOneActive($slug)
    {
        try {
            $query = "
                SELECT uuid, category_uuid, name, slug, status, created_at, updated_at
                FROM subcategories
                WHERE slug = :slug
                    AND deleted = :deleted
                    AND status = :status
            ";

            $stmt = $this->openDb()->prepare($query);
            $stmt->bindValue(":slug", $slug);
            $stmt->bindValue(":deleted", '0');
            $stmt->bindValue(":status", '1');
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = null;
            $this->closeDb();

            return $result;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function getOneActiveByUuid($uuid)
    {
        try {
            $query = "
                SELECT uuid, category_uuid, name, slug, status, created_at, updated_at
                FROM subcategories
                WHERE uuid = :uuid
                    AND deleted = :deleted
                    AND status = :status
            ";

            $stmt = $this->openDb()->prepare($query);
            $stmt->bindValue(":uuid", $uuid);
            $stmt->bindValue(":deleted", '0');
            $stmt->bindValue(":status", '1');
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = null;
            $this->closeDb();

            return $result;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function getAll($categoryUuid): bool|array|string
    {
        try {
            $query = "
                 SELECT uuid, category_uuid, name, slug, status, created_at, updated_at
                FROM subcategories
                WHERE deleted = :deleted
                    AND category_uuid = :category_uuid
                ORDER BY name";

            $stmt = $this->openDb()->prepare($query);
            $stmt->bindValue(":deleted", '0');
            $stmt->bindValue(":category_uuid", $categoryUuid);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = null;
            $this->closeDb();

            return $result;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function getAllActives($categoryUuid): bool|array|string
    {
        try {
            $query = "
                 SELECT uuid, category_uuid, name, slug, status, created_at, updated_at
                FROM subcategories
                WHERE status = :status 
                    AND deleted = :deleted
                    AND category_uuid = :category_uuid
                ORDER BY name";

            $stmt = $this->openDb()->prepare($query);
            $stmt->bindValue(":status", '1');
            $stmt->bindValue(":deleted", '0');
            $stmt->bindValue(":category_uuid", $categoryUuid);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = null;
            $this->closeDb();

            return $result;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function subcategoryExists($field, $value, $uuidField, $catUuid, $uuid = null): bool|string
    {
        try {
            if (!empty($uuid)) {
                $where = " AND $uuidField != '$uuid' ";
            } else {
                $where = "";
            }

            $query = "
                SELECT $uuidField FROM subcategories 
                WHERE $field = :value $where AND category_uuid = :category_uuid";
            $stmt = $this->openDb()->prepare($query);
            $stmt->bindValue(":value", $value);
            $stmt->bindValue(":category_uuid", $catUuid);
            $stmt->execute();

            if ($stmt->rowCount() >= 1) {
                $result = true;
            } else {
                $result = false;
            }

            $stmt = null;
            $this->closeDb();

            return $result;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function getTotalByCategory($category_uuid)
    {
        try {
            $query = "SELECT COUNT(id) as total
                        FROM subcategories 
                        WHERE category_uuid = :category_uuid
                        AND deleted = :deleted";

            $stmt = $this->openDb()->prepare($query);
            $stmt->bindValue(":category_uuid", $category_uuid);
            $stmt->bindValue(":deleted", '0');
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = null;
            $this->closeDb();

            if ($result) {
                return $result['total'];
            } else {
                return 0;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}