<?php

namespace Admin\Model;

use Core\Db\Model;
use Exception;
use PDO;

class Imports extends Model
{
    public function __construct()
    {
        $this->setTable('imports');
    }

    public function getOne(string $uuid): bool|array|string
    {
        try {
            $query = "
                 SELECT i.uuid, i.module, i.created_at,
                        i.total, u.name as userName
                FROM imports AS i
                INNER JOIN user AS u
                    ON i.user_uuid = u.uuid
                WHERE i.uuid = :uuid
                    AND i.deleted = :deleted";

            $stmt = $this->openDb()->prepare($query);
            $stmt->bindValue(":uuid", $uuid);
            $stmt->bindValue(":deleted", '0');
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
                 SELECT i.uuid, i.module, i.created_at,
                        i.total, u.name as userName
                FROM imports AS i
                INNER JOIN user AS u
                    ON i.user_uuid = u.uuid
                WHERE i.deleted = :deleted
                ORDER BY i.created_at DESC";

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
}