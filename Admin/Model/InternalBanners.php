<?php

namespace Admin\Model;

use Core\Db\Model;
use Exception;
use PDO;

class InternalBanners extends Model
{
    public function __construct()
    {
        $this->setTable('internal_banners');
    }

    public function getOne()
    {
        try {
            $query = "
                SELECT id, file_about, file_publications, 
                        file_contact, file_services, 
                        file_politics, file_use_terms,
                        file_customers, file_properties, updated_at
                FROM internal_banners
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
