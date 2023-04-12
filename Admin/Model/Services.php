<?php

namespace Admin\Model;

use Core\Db\Model;
use Exception;
use PDO;

class Services extends Model
{
    public function __construct()
    {
        $this->setTable('services');
    }

    public function getOne($uuid)
    {
        try {
            $query = "
                SELECT uuid, title, slug, description,
                        status, created_at, updated_at
                FROM services
                WHERE uuid = :uuid AND deleted = '0'
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
                SELECT uuid, title, slug, description,
                        status, created_at, updated_at
                FROM services AS p
                WHERE deleted = '0'
                ORDER BY uuid DESC
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
                SELECT p.uuid, p.title, p.slug, p.description,
                        p.status, p.created_at, p.updated_at
                FROM services AS p
                WHERE p.deleted = '0'
                    AND p.status = '1'
                ORDER BY p.id
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

    public function getAllRand($limit, $notSlug): bool|array|string
    {
        try {
            $query = "
                SELECT p.uuid, p.title, p.slug, p.description,
                        p.status, p.created_at, p.updated_at
                FROM services AS p
                WHERE p.status = '1' AND p.slug != '$notSlug'
                AND p.deleted = '0'
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

    public function getAllRands($limit): bool|array|string
    {
        try {
            $query = "
                SELECT p.uuid, p.title, p.slug, p.description,
                        p.status, p.created_at, p.updated_at
                FROM services AS p
                WHERE p.status = '1'
                AND p.deleted = '0'
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
            $query = " SELECT p.uuid, p.title, p.slug, p.description,
                                p.status, p.created_at, p.updated_at
                        FROM services AS p
                        WHERE p.slug = :slug
                            AND p.deleted = '0'";

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
            $query = " SELECT p.uuid, p.title, p.slug, p.description,
                                p.status, p.created_at, p.updated_at
                        FROM services AS p
                        WHERE p.deleted = '0'
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
                FROM services
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

    public function totalServices(): int|string
    {
        try {
            $query = "
                SELECT uuid
                FROM services
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

    public function totalActiveServices(): int|string
    {
        try {
            $query = "
                SELECT uuid
                FROM services
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