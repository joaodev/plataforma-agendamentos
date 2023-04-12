<?php

namespace Admin\Model;

use Core\Db\Model;
use Exception;
use PDO;

class Categories extends Model
{
    public function __construct()
    {
        $this->setTable('categories');
    }

    public function getOne($uuid)
    {
        try {
            $query = "
                SELECT uuid, name, slug, status, created_at, updated_at, cat_type
                FROM categories
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
                SELECT uuid, name, slug, status, created_at, updated_at, cat_type
                FROM categories
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
                SELECT uuid, name, slug, status, created_at, updated_at, cat_type
                FROM categories
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

    public function getAll(): bool|array|string
    {
        try {
            $query = "
                 SELECT uuid, name, slug, status, created_at, updated_at, cat_type
                FROM categories
                WHERE deleted = :deleted
                ORDER BY name";

            $stmt = $this->openDb()->prepare($query);
            $stmt->bindValue(":deleted", '0');
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = null;
            $this->closeDb();

            return $result;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function getAllActives(): bool|array|string
    {
        try {
            $query = "
                 SELECT uuid, name, slug, status, created_at, updated_at, cat_type
                FROM categories
                WHERE deleted = :deleted AND status = :status
                ORDER BY name";

            $stmt = $this->openDb()->prepare($query);
            $stmt->bindValue(":deleted", '0');
            $stmt->bindValue(":status", '1');
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = null;
            $this->closeDb();

            return $result;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function getAllActivesByType($type): bool|array|string
    {
        try {
            $query = "
                 SELECT uuid, name, slug, status, created_at, updated_at, cat_type
                FROM categories
                WHERE deleted = :deleted AND status = :status
                AND cat_type = :type
                ORDER BY name";

            $stmt = $this->openDb()->prepare($query);
            $stmt->bindValue(":deleted", '0');
            $stmt->bindValue(":status", '1');
            $stmt->bindValue(":type", $type);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = null;
            $this->closeDb();

            return $result;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function getCategoriesWithItems($table): bool|array|string
    {
        try {
            $query = "
                SELECT DISTINCT c.uuid, c.name, c.slug, cat_type
                FROM categories AS c
                INNER JOIN $table AS p 
                    ON c.uuid = p.category_uuid
                WHERE c.deleted = :deleted 
                    AND c.status = :status
                ORDER BY c.name";

            $stmt = $this->openDb()->prepare($query);
            $stmt->bindValue(":deleted", '0');
            $stmt->bindValue(":status", '1');
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = null;
            $this->closeDb();

            return $result;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function getRandActives($limit): bool|array|string
    {
        try {
            $query = "
                SELECT DISTINCT c.uuid, c.name, c.slug, cat_type
                FROM categories AS c
                INNER JOIN blog AS p ON c.uuid = p.category_uuid
                WHERE c.deleted = :deleted AND c.status = :status
                ORDER BY RAND() LIMIT $limit";

            $stmt = $this->openDb()->prepare($query);
            $stmt->bindValue(":deleted", '0');
            $stmt->bindValue(":status", '1');
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = null;
            $this->closeDb();

            return $result;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function totalCategories(): int|string
    {
        try {
            $query = "
                SELECT uuid
                FROM categories 
                WHERE deleted = '0'
            ";

            $stmt = $this->openDb()->query($query);
            $result = $stmt->rowCount();

            $stmt = null;
            $this->closeDb();

            return $result;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function getOneByName($name)
    {
        try {
            $query = "
                SELECT uuid, name, slug, status, cat_type
                FROM categories
                WHERE name LIKE '%$name%'
            ";

            $stmt = $this->openDb()->prepare($query);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = null;
            $this->closeDb();

            return $result;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}