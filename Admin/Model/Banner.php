<?php

namespace Admin\Model;

use Core\Db\Model;
use Exception;
use PDO;

class Banner extends Model
{
    public function __construct()
    {
        $this->setTable('banner');
    }

    public function getOne($uuid)
    {
        try {
            $query = "
                SELECT uuid, title, caption, file, 
                        link_1, title_link_1, title_link_2, link_2,
                        status, created_at, updated_at
                FROM banner
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
                SELECT uuid, title, caption, file, 
                        link_1, title_link_1, title_link_2, link_2,
                        status, created_at, updated_at
                FROM banner
                WHERE deleted = '0'
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

    public function getAllActives(): bool|array|string
    {
        try {
            $query = "
                SELECT uuid, title, caption, file, 
                        link_1, title_link_1, title_link_2, link_2,
                        status, created_at, updated_at
                FROM banner
                WHERE deleted = '0' AND status = '1'
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
}