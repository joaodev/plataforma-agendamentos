<?php

namespace Admin\Model;

use Core\Db\Model;
use Exception;
use PDO;

class Logs extends Model
{
    public function __construct()
    {
        $this->setTable('logs');
    }

    public function getAll(): bool|array|string
    {
        try {
            $query = "
                 SELECT l.uuid, l.log_user_uuid, l.log_action, l.log_date, 
                        l.log_ip, l.log_user_agent, l.log_status, 
                        u.name as username
                FROM logs AS l
                    INNER JOIN user AS u 
                        ON l.log_user_uuid = u.uuid
                ORDER BY l.log_date DESC";

            $stmt = $this->openDb()->prepare($query);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = null;
            $this->closeDb();

            return $result;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function getOne($uuid)
    {
        try {
            $query = "
                 SELECT l.uuid, l.log_user_uuid, l.log_action, l.log_date, 
                        l.log_ip, l.log_user_agent, l.log_status, 
                        u.name as username
                FROM logs AS l
                    INNER JOIN user AS u 
                        ON l.log_user_uuid = u.uuid
                WHERE l.uuid = :uuid";

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
}