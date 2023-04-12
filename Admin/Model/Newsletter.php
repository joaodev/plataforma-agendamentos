<?php

namespace Admin\Model;

use Core\Db\Model;
use Exception;
use PDO;

class Newsletter extends Model
{
    public function __construct()
    {
        $this->setTable('newsletter');
    }

    public function getOne($uuid)
    {
        try {
            $query = "
                SELECT uuid, name, email, cellphone, created_at
                FROM newsletter
                WHERE uuid = :uuid AND deleted = :deleted
            ";

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
                 SELECT uuid, name, email, cellphone, created_at
                FROM newsletter
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

    public function totalEmails(): int|string
    {
        try {
            $query = "
                SELECT uuid
                FROM newsletter 
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
}