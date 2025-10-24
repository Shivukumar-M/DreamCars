<?php

class RentalService {

    public static function getCars() {
        $query = "SELECT * FROM cars";

        $stmt = Database::getInstance()
            ->getDb()
            ->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getCar($id) {
        $query = "SELECT *  FROM cars WHERE _id = :id ";

        $stmt = Database::getInstance()
            ->getDb()
            ->prepare($query);

        $stmt->bindParam(":id", $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getCarDetails($id) {
        $query = "SELECT *  FROM cars, car_rates WHERE _id = :id AND car_id = :id";

        $stmt = Database::getInstance()
            ->getDb()
            ->prepare($query);

        $stmt->bindParam(":id", $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function removeRental($id) {
        $db = Database::getInstance()->getDb();
        
        try {
            $db->beginTransaction();

            // Get rental details first to update car stock
            $stmt = $db->prepare("SELECT car_id FROM rentals WHERE _id = ?");
            $stmt->execute([$id]);
            $rental = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($rental) {
                // Update car stock
                $updateStmt = $db->prepare("UPDATE cars SET stock = stock + 1 WHERE _id = ?");
                $updateStmt->execute([$rental['car_id']]);

                // Delete from rentals table
                $deleteStmt = $db->prepare("DELETE FROM rentals WHERE _id = ?");
                $deleteStmt->execute([$id]);
            }

            // Also delete from transaction table (existing logic)
            $query = "DELETE from transaction WHERE `_id` = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();

            $db->commit();
            return true;

        } catch (PDOException $e) {
            $db->rollBack();
            error_log("Rental removal error: " . $e->getMessage());
            return false;
        }
    }

    public static function getRentalsForUser($id) {
        $query = "SELECT transaction._id, cars.`_id` as car_id, mode, value, name, pic, rate_by_hour, rate_by_day, rate_by_km, first_name, last_name, date_format(time, '%D %b %Y, %I:%i %p') as time FROM transaction, cars, user, car_rates where transaction.car_id = cars.`_id` AND user.`_id` = transaction.user_id AND car_rates.car_id = cars.`_id` AND user.`_id` = :id";

        $stmt = Database::getInstance()
            ->getDb()
            ->prepare($query);

        $stmt->bindParam(":id", $id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getRentals() {
        $query = "SELECT transaction._id, cars.`_id` as car_id, mode, value, name, pic, rate_by_hour, rate_by_day, rate_by_km, first_name, last_name, date_format(time, '%D %b %Y, %I:%i %p') as time FROM transaction, cars, user, car_rates where transaction.car_id = cars.`_id` AND user.`_id` = transaction.user_id AND car_rates.car_id = cars.`_id`";

        $stmt = Database::getInstance()
            ->getDb()
            ->prepare($query);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // NEW METHOD: Get all rentals from rentals table for admin dashboard
    public static function getAllRentals() {
        $db = Database::getInstance()->getDb();
        
        $stmt = $db->query("
            SELECT 
                r._id,
                r.user_id,
                r.car_id,
                r.mode,
                r.value,
                r.amount,
                r.start_time,
                r.end_time,
                r.status,
                r.created_at,
                u.first_name,
                u.last_name,
                u.email,
                c.name as car_name,
                c.pic as car_image
            FROM rentals r
            LEFT JOIN user u ON r.user_id = u._id
            LEFT JOIN cars c ON r.car_id = c._id
            ORDER BY r.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function insertRental($transactionArray) {
        $fields = ['user_id', 'car_id', 'mode', 'value'];

        $db = Database::getInstance()->getDb();

        try {
            $db->beginTransaction();

            $query = 'SELECT stock FROM cars WHERE _id = :id';
            $stmt = $db->prepare($query);
            $stmt->bindParam(":id", $transactionArray['car_id']);
            $stmt->execute();

            if($stmt->fetchColumn() > 0){
                // 1. Insert into transaction table (existing logic)
                $query = 'INSERT INTO transaction(' . implode(',', $fields) . ') VALUES(:' . implode(',:', $fields) . ')';
                $stmt = $db->prepare($query);

                $prepared_array = array();
                foreach ($fields as $field) {
                    $prepared_array[':' . $field] = @$transactionArray[$field];
                }

                $stmt->execute($prepared_array);
                $transactionId = Database::getInstance()->getDb()->lastInsertId();

                // 2. Insert into rentals table (NEW - for admin dashboard)
                // Calculate amount based on mode and car rates
                $car = self::getCarDetails($transactionArray['car_id']);
                $amount = 0;
                
                switch($transactionArray['mode']) {
                    case 'hour':
                        $amount = $car['rate_by_hour'] * $transactionArray['value'];
                        break;
                    case 'day':
                        $amount = $car['rate_by_day'] * $transactionArray['value'];
                        break;
                    case 'km':
                        $amount = $car['rate_by_km'] * $transactionArray['value'];
                        break;
                }

                $rentalStmt = $db->prepare("
                    INSERT INTO rentals (user_id, car_id, mode, value, amount, start_time, status, created_at) 
                    VALUES (?, ?, ?, ?, ?, NOW(), 'active', NOW())
                ");
                $rentalStmt->execute([
                    $transactionArray['user_id'],
                    $transactionArray['car_id'],
                    $transactionArray['mode'],
                    $transactionArray['value'],
                    $amount
                ]);
                $rentalId = $db->lastInsertId();

                // 3. Update car stock
                $updateStmt = $db->prepare("UPDATE cars SET stock = stock - 1 WHERE _id = ?");
                $updateStmt->execute([$transactionArray['car_id']]);

            } else {
                return 0;
            }

            $db->commit();
        } catch (PDOException $ex) {
            $db->rollBack();
            return $ex->getMessage();
        }

        return $rentalId; // Return rental ID for success message
    }

}