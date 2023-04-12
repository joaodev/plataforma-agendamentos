<?php

namespace Admin\Model;

use Core\Db\Model;
use Exception;
use PDO;

class About extends Model
{
    public function __construct()
    {
        $this->setTable('about');
    }

    public function getOne()
    {
        try {
            $query = "
                SELECT *
                FROM about
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