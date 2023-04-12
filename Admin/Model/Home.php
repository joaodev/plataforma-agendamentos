<?php

namespace Admin\Model;

use Core\Db\Model;
use Exception;
use PDO;

class Home extends Model
{
    public function __construct()
    {
        $this->setTable('home');
    }

    public function getOne()
    {
        try {
            $query = "
                SELECT id,
                        footer_title, footer_description, updated_at,
                        file_services, file_newsletter
                FROM home
                WHERE id = :id
            ";

            $stmt = $this->openDb()->prepare($query);
            $stmt->bindValue(":id", 1);
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