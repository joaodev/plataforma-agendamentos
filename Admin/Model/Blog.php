<?php

namespace Admin\Model;

use Core\Db\Model;
use Exception;
use PDO;

class Blog extends Model
{
    public function __construct()
    {
        $this->setTable('blog');
    }

    public function getOne($uuid)
    {
        try {
            $query = "
                SELECT p.uuid, p.title, p.slug, p.subtitle, p.description,  
                        p.source_title, p.source_link,
                        p.category_uuid, p.subcategory_uuid,
                        p.status, p.created_at, p.updated_at,
                        u.name as author,
                        c.name as category, s.name as subcategory
                FROM blog AS p
                INNER JOIN user AS u
                    ON p.user_uuid = u.uuid
                INNER JOIN categories AS c
                    ON p.category_uuid = c.uuid
                LEFT JOIN subcategories AS s
                    ON p.subcategory_uuid = s.uuid
                WHERE p.uuid = :uuid
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

    public function getAll(): bool|array|string
    {
        try {
            $query = "
                SELECT p.uuid, p.title, p.slug, p.subtitle, p.description,  
                        p.source_title, p.source_link,
                        p.category_uuid, p.subcategory_uuid,
                        p.status, p.created_at, p.updated_at,
                        u.name as author
                FROM blog AS p
                INNER JOIN user AS u
                    ON p.user_uuid = u.uuid
                WHERE p.deleted = '0' AND u.deleted = '0'
                ORDER BY p.uuid DESC
            ";

            $stmt = $this->openDb()->query($query);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = null;
            $this->closeDb();

            return $result;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function getAllActives($start = null, $itemsLimit = null): bool|array|string
    {
        try {
            $query = "
                SELECT p.uuid, p.title, p.slug, p.subtitle, p.description,
                        p.source_title, p.source_link,
                        p.category_uuid, p.subcategory_uuid,
                        p.status, p.created_at, p.updated_at,
                        u.name as author
                FROM blog AS p
                INNER JOIN user AS u
                    ON p.user_uuid = u.uuid
                WHERE p.deleted = '0' AND u.deleted = '0'
                    AND p.status = '1'
                ORDER BY p.created_at DESC 
            ";

            if ($start !== null && $itemsLimit !== null) {
                $query .= " LIMIT $start, $itemsLimit ";
            }

            $stmt = $this->openDb()->query($query);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = null;
            $this->closeDb();

            return $result;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }


    public function getAllBySearch($category, $subcategory, $keyword, $start = null, $itemsLimit = null): bool|array|string
    {
        try {
            $whereCond = '';
            if ($category != '-') {
                $whereCond .= " AND p.category_uuid = :category";
            }

            if ($subcategory != '-') {
                $whereCond .= " AND p.subcategory_uuid = :subcategory";
            }

            if ($keyword != '-') {
                $whereCond .= "  AND (p.title LIKE '%$keyword%' OR p.description LIKE '%$keyword%')";
            }

            $query = "
                SELECT p.uuid, p.title, p.slug, p.subtitle, p.description,
                        p.source_title, p.source_link,
                        p.category_uuid, p.subcategory_uuid,
                        p.status, p.created_at, p.updated_at,
                        c.name as category, s.name as subcategory,
                        u.name as author
                FROM blog AS p
                INNER JOIN user AS u
                    ON p.user_uuid = u.uuid
                INNER JOIN categories AS c
                    ON p.category_uuid = c.uuid
                LEFT JOIN subcategories AS s
                    ON p.subcategory_uuid = s.uuid
                WHERE p.deleted = :deleted AND p.status = :status
                    $whereCond
                ORDER BY p.created_at DESC ";

            if ($start !== null && $itemsLimit !== null) {
                $query .= " LIMIT $start, $itemsLimit ";
            }

            $stmt = $this->openDb()->prepare($query);
            $stmt->bindValue(":deleted", '0');
            $stmt->bindValue(":status", '1');

            if ($category != '-') {
                $stmt->bindValue(":category", $category);
            }

            if ($subcategory != '-') {
                $stmt->bindValue(":subcategory", $subcategory);
            }

            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = null;
            $this->closeDb();

            return $result;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function getAllRand($limit, $notSlug): bool|array|string
    {
        try {
            $query = "
                SELECT p.uuid, p.title, p.slug, p.subtitle, p.description,
                        p.source_title, p.source_link,
                        p.category_uuid, p.subcategory_uuid,
                        p.status, p.created_at, p.updated_at,
                        u.name as author
                FROM blog AS p
                INNER JOIN user AS u
                    ON p.user_uuid = u.uuid
                WHERE p.status = '1' AND p.slug != '$notSlug'
                AND p.deleted = '0' AND u.deleted = '0'
                ORDER BY RAND() LIMIT $limit
            ";

            $stmt = $this->openDb()->query($query);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = null;
            $this->closeDb();

            return $result;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function getOneBySlug($slug)
    {
        try {
            $query = " SELECT p.uuid, p.title, p.slug, p.subtitle, p.description, 
                                p.source_title, p.source_link,
                                p.category_uuid, p.subcategory_uuid,
                                p.status, p.created_at, p.updated_at,
                                c.name as category, s.name as subcategory,
                                u.name as author
                        FROM blog AS p
                        INNER JOIN user AS u
                            ON p.user_uuid = u.uuid
                        INNER JOIN categories AS c
                            ON p.category_uuid = c.uuid
                        LEFT JOIN subcategories AS s
                            ON p.subcategory_uuid = s.uuid
                        WHERE p.slug = :slug 
                            AND p.deleted = '0' AND u.deleted = '0'";

            $stmt = $this->openDb()->prepare($query);
            $stmt->bindValue(":slug", $slug);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = null;
            $this->closeDb();

            return $result;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function getLastActive()
    {
        try {
            $query = " SELECT p.uuid, p.title, p.slug, p.subtitle, p.description,
                                p.source_title, p.source_link,
                                p.category_uuid, p.subcategory_uuid,
                                p.status, p.created_at, p.updated_at,
                                u.name as author
                        FROM blog AS p
                        INNER JOIN user AS u
                            ON p.user_uuid = u.uuid
                        WHERE p.deleted = '0' AND u.deleted = '0'
                            AND p.status = '1'
                        ORDER BY p.created_at DESC";

            $stmt = $this->openDb()->query($query);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = null;
            $this->closeDb();

            return $result;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function totalPublications(): int|string
    {
        try {
            $query = "
                SELECT uuid
                FROM blog 
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

    public function totalActivePublications(): int|string
    {
        try {
            $query = "
                SELECT uuid
                FROM blog 
                WHERE deleted = '0'
                AND status = '1'
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
}
