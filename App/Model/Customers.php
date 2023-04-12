<?php

namespace App\Model;

use Core\Db\Model;
use Core\Db\Bcrypt;
use Exception;
use PDO;

class Customers extends Model
{
    public function __construct()
    {
        $this->setTable('customers');
    }

    public function getOne($uuid)
    {
        try {
            $query = "
                SELECT c.uuid, c.name,
                        c.email, c.password, c.phone, c.cellphone, 
                        c.postal_code, c.address, c.number, c.complement, 
                        c.neighborhood, c.city, c.state
                FROM customers AS c
                WHERE c.uuid = :uuid AND c.deleted = :deleted
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

    public function findByCrenditials($email, $password)
    {
        if (!empty($email) && !empty($password)) {
            try {
                $query = "
                    SELECT c.uuid, c.name, c.email, c.password, c.cellphone
                    FROM customers AS c
                    WHERE c.email=:email
                        AND c.status = :status AND c.deleted = :deleted";

                $stmt = $this->openDb()->prepare($query);
                $stmt->bindValue(":email", $email);
                $stmt->bindValue(":status", '1');
                $stmt->bindValue(":deleted", '0');
                $stmt->execute();
              
                if ($stmt->rowCount() == 1) {
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    if (!empty($user['password']) && Bcrypt::check($password, $user['password'])) {
                        $data = $user;
                    } else {
                        $data = false;
                    }
                } else {
                    $data = false;
                }

                $stmt = null;
                $this->closeDb();

                return $data;
            } catch (Exception $e) {
                return $e->getMessage();
            }
        } else {
            return false;
        }
    }

    public function getInterest(string $uuid, string $customerUuid): bool|array|string
    {
        try {
            $query = "
                SELECT pi.id, pi.uuid, pi.property_uuid, pi.customer_uuid, 
                       pi.description, pi.status, pi.created_at, pi.updated_at,
                       p.title as propertyName, 
                       c.name as customerName, c.email as customerEmail, 
                       c.cellphone as customerCellphone            
                FROM property_interests AS pi
                INNER JOIN properties AS p 
                    ON pi.property_uuid = p.uuid
                INNER JOIN customers AS c 
                    ON pi.customer_uuid = c.uuid
                WHERE pi.deleted = :deleted
                    AND pi.customer_uuid = :customer_uuid
                    AND pi.uuid = :uuid
                ORDER BY pi.created_at DESC";

            $stmt = $this->openDb()->prepare($query);
            $stmt->bindValue(":deleted", '0');
            $stmt->bindValue(":customer_uuid", $customerUuid);
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

    public function getInterests(string $customerUuid): bool|array|string
    {
        try {
            $query = "
                SELECT pi.id, pi.uuid, pi.property_uuid, pi.customer_uuid, 
                       pi.description, pi.status, pi.created_at, pi.updated_at,
                       p.title as propertyName, 
                       c.name as customerName, c.email as customerEmail,
                       c.cellphone as customerCellphone       
                FROM property_interests AS pi
                INNER JOIN properties AS p 
                    ON pi.property_uuid = p.uuid
                INNER JOIN customers AS c 
                    ON pi.customer_uuid = c.uuid
                WHERE pi.deleted = :deleted
                    AND pi.customer_uuid = :customer_uuid
                ORDER BY pi.created_at DESC";

            $stmt = $this->openDb()->prepare($query);
            $stmt->bindValue(":deleted", '0');
            $stmt->bindValue(":customer_uuid", $customerUuid);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = null;
            $this->closeDb();

            return $result;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function getSchedule(string $uuid, string $customerUuid): bool|array|string
    {
        try {
            $query = "SELECT s.uuid, s.property_uuid, s.interest_uuid, s.customer_uuid, 
                        s.user_uuid, s.description, s.schedule_date, s.schedule_time, 
                        s.status, s.created_at, s.updated_at,
                        p.title as propertyTitle,
                        c.name as customerName,
                        u.name as userName
                        FROM schedules AS s
                        INNER JOIN properties AS p ON s.property_uuid = p.uuid
                        INNER JOIN customers AS c ON s.customer_uuid = c.uuid
                        LEFT JOIN user AS u ON s.user_uuid = u.uuid
                        WHERE s.deleted = :deleted
                            AND s.customer_uuid = :customer_uuid
                            AND s.uuid = :uuid";

            $stmt = $this->openDb()->prepare($query);
            $stmt->bindValue(":deleted", '0');
            $stmt->bindValue(":customer_uuid", $customerUuid);
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

    public function getSchedules(string $customerUuid): bool|array|string
    {
        try {
            $query = "SELECT s.uuid, s.property_uuid, s.interest_uuid, s.customer_uuid, 
                        s.user_uuid, s.description, s.schedule_date, s.schedule_time, 
                        s.status, s.created_at, s.updated_at,
                        p.title as propertyTitle,
                        c.name as customerName,
                        u.name as userName
                        FROM schedules AS s
                        INNER JOIN properties AS p ON s.property_uuid = p.uuid
                        INNER JOIN customers AS c ON s.customer_uuid = c.uuid
                        LEFT JOIN user AS u ON s.user_uuid = u.uuid
                        WHERE s.deleted = :deleted
                            AND s.customer_uuid = :customer_uuid";

            $stmt = $this->openDb()->prepare($query);
            $stmt->bindValue(":deleted", '0');
            $stmt->bindValue(":customer_uuid", $customerUuid);
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