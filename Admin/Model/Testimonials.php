<?php

namespace Admin\Model;

use Core\Db\Model;
use Exception;
use PDO;

class Testimonials extends Model
{
    public function __construct()
    {
        $this->setTable('testimonials');
    }

    public function getOne($uuid)
    {
        try {
            $query = "
                SELECT uuid, name, occupation, description, file, 
                        status, created_at, updated_at
                FROM testimonials
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
                SELECT uuid, name, occupation, description, file, 
                        status, created_at, updated_at
                FROM testimonials
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
                SELECT uuid, name, occupation, description, file, 
                        status, created_at, updated_at
                FROM testimonials
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

    public function totalTestimonials(): int|string
    {
        try {
            $query = "
                SELECT uuid
                FROM testimonials 
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