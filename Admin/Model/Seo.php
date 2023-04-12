<?php

namespace Admin\Model;

use Core\Db\Model;
use Exception;
use PDO;

class Seo extends Model
{
    public function __construct()
    {
        $this->setTable('seo');
    }

    public function getOne()
    {
        try {
            $query = "
                SELECT id, title, description, about_title, about_description,
                        blog_title, blog_description, contact_title, contact_description,
                        services_title, services_description,
                        customers_title, customers_description, privacy_title, privacy_description,
                        useterms_title, useterms_description
                FROM seo
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
