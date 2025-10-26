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
        $query = "SELECT * FROM cars WHERE _id = :id";

        $stmt = Database::getInstance()
            ->getDb()
            ->prepare($query);

        $stmt->bindParam(":id", $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getCarDetails($id) {
        $db = Database::getInstance()->getDb();
        
        try {
            // Get car with rates, handling all car table columns
            $query = "SELECT c.*, 
                      COALESCE(cr.rate_by_hour, 100) as rate_by_hour, 
                      COALESCE(cr.rate_by_day, 2000) as rate_by_day, 
                      COALESCE(cr.rate_by_km, 20) as rate_by_km 
                      FROM cars c 
                      LEFT JOIN car_rates cr ON c._id = cr.car_id 
                      WHERE c._id = :id";

            $stmt = $db->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            
            $car = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($car) {
                error_log("✓ Car details for ID $id: " . $car['name'] . 
                         " | Rates: ₹" . $car['rate_by_hour'] . "/hr, ₹" . 
                         $car['rate_by_day'] . "/day, ₹" . $car['rate_by_km'] . "/km");
            } else {
                error_log("✗ Car not found with ID: $id");
            }
            
            return $car;
            
        } catch (PDOException $e) {
            error_log("✗ Error fetching car details: " . $e->getMessage());
            return false;
        }
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
                
                error_log("✓ Rental removed: ID = " . $id . ", Car stock updated for car ID = " . $rental['car_id']);
            } else {
                error_log("✗ Rental not found for removal: ID = " . $id);
            }

            $db->commit();
            return true;

        } catch (PDOException $e) {
            $db->rollBack();
            error_log("✗ Rental removal error: " . $e->getMessage());
            return false;
        }
    }

    public static function getRentalsForUser($id) {
        $db = Database::getInstance()->getDb();
        
        try {
            // Query the RENTALS table with LEFT JOIN to handle missing rates
            $stmt = $db->prepare("
                SELECT 
                    r._id,
                    r.car_id,
                    r.mode,
                    r.value,
                    r.amount,
                    r.start_time as time,
                    r.status,
                    c.name,
                    c.pic,
                    COALESCE(cr.rate_by_hour, 100) as rate_by_hour,
                    COALESCE(cr.rate_by_day, 2000) as rate_by_day,
                    COALESCE(cr.rate_by_km, 20) as rate_by_km,
                    u.first_name,
                    u.last_name,
                    DATE_FORMAT(r.start_time, '%D %b %Y, %I:%i %p') as formatted_time
                FROM rentals r
                JOIN cars c ON r.car_id = c._id
                LEFT JOIN car_rates cr ON r.car_id = cr.car_id
                JOIN user u ON r.user_id = u._id
                WHERE r.user_id = :id
                ORDER BY r.start_time DESC
            ");
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            
            $rentals = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            error_log("✓ Found " . count($rentals) . " rentals in RENTALS table for user " . $id);
            
            return $rentals;

        } catch (PDOException $e) {
            error_log("✗ Error fetching rentals for user: " . $e->getMessage());
            return [];
        }
    }

    public static function getRentals() {
        $db = Database::getInstance()->getDb();
        
        try {
            $stmt = $db->prepare("
                SELECT 
                    r._id,
                    r.car_id,
                    r.mode,
                    r.value,
                    r.amount,
                    r.start_time as time,
                    r.status,
                    c.name,
                    c.pic,
                    u.first_name,
                    u.last_name,
                    COALESCE(cr.rate_by_hour, 100) as rate_by_hour,
                    COALESCE(cr.rate_by_day, 2000) as rate_by_day,
                    COALESCE(cr.rate_by_km, 20) as rate_by_km,
                    DATE_FORMAT(r.start_time, '%D %b %Y, %I:%i %p') as formatted_time
                FROM rentals r
                JOIN cars c ON r.car_id = c._id
                LEFT JOIN car_rates cr ON r.car_id = cr.car_id
                JOIN user u ON r.user_id = u._id
                ORDER BY r.start_time DESC
            ");
            $stmt->execute();
            $rentals = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            error_log("✓ Found " . count($rentals) . " rentals in RENTALS table for all users");
            
            return $rentals;

        } catch (PDOException $e) {
            error_log("✗ Error fetching rentals: " . $e->getMessage());
            return [];
        }
    }

    public static function getAllRentals() {
        $db = Database::getInstance()->getDb();
        
        try {
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
                    c.pic as car_image,
                    COALESCE(cr.rate_by_hour, 100) as rate_by_hour,
                    COALESCE(cr.rate_by_day, 2000) as rate_by_day,
                    COALESCE(cr.rate_by_km, 20) as rate_by_km
                FROM rentals r
                LEFT JOIN user u ON r.user_id = u._id
                LEFT JOIN cars c ON r.car_id = c._id
                LEFT JOIN car_rates cr ON r.car_id = cr.car_id
                ORDER BY r.created_at DESC
            ");
            $rentals = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            error_log("✓ Admin: Found " . count($rentals) . " rentals in RENTALS table");
            
            return $rentals;
            
        } catch (PDOException $e) {
            error_log("✗ Error fetching all rentals for admin: " . $e->getMessage());
            return [];
        }
    }

    public static function insertRental($transactionArray) {
        $db = Database::getInstance()->getDb();

        try {
            $db->beginTransaction();

            // Check car stock
            $query = 'SELECT stock FROM cars WHERE _id = :id';
            $stmt = $db->prepare($query);
            $stmt->bindParam(":id", $transactionArray['car_id']);
            $stmt->execute();

            $stock = $stmt->fetchColumn();
            
            if($stock > 0) {
                // Get car details with rates
                $car = self::getCarDetails($transactionArray['car_id']);
                
                if (!$car) {
                    throw new Exception("Car not found with ID: " . $transactionArray['car_id']);
                }

                // Calculate amount based on mode and car rates
                $rate_field = "rate_by_" . $transactionArray['mode'];
                
                if (!isset($car[$rate_field])) {
                    throw new Exception("Invalid rental mode: " . $transactionArray['mode']);
                }
                
                $rate = $car[$rate_field];
                $amount = $rate * $transactionArray['value'];

                error_log("=== RENTAL CALCULATION ===");
                error_log("Car ID: " . $transactionArray['car_id']);
                error_log("Mode: " . $transactionArray['mode']);
                error_log("Rate: " . $rate);
                error_log("Value: " . $transactionArray['value']);
                error_log("Amount: " . $amount);

                // INSERT INTO RENTALS TABLE ONLY (No duplicate insertion)
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

                // Update car stock
                $updateStmt = $db->prepare("UPDATE cars SET stock = stock - 1 WHERE _id = ?");
                $updateStmt->execute([$transactionArray['car_id']]);

                error_log("✓ Booking successful: Rental ID = " . $rentalId . 
                         ", User ID = " . $transactionArray['user_id'] . ", Amount = ₹" . $amount);

                $db->commit();
                return $rentalId;
                
            } else {
                $db->rollBack();
                error_log("✗ Booking failed: No stock available for car ID " . $transactionArray['car_id']);
                return 0;
            }

        } catch (Exception $ex) {
            $db->rollBack();
            error_log("✗ Rental insertion error: " . $ex->getMessage());
            return $ex->getMessage();
        }
    }

    // Optional: If you still need transaction table insertion for other purposes
    public static function insertTransactionOnly($transactionArray) {
        $fields = ['user_id', 'car_id', 'mode', 'value'];
        $db = Database::getInstance()->getDb();

        try {
            $query = 'INSERT INTO transaction(' . implode(',', $fields) . ') VALUES(:' . implode(',:', $fields) . ')';
            $stmt = $db->prepare($query);

            $prepared_array = array();
            foreach ($fields as $field) {
                $prepared_array[':' . $field] = @$transactionArray[$field];
            }

            $stmt->execute($prepared_array);
            $transactionId = $db->lastInsertId();

            error_log("✓ Transaction recorded: ID = " . $transactionId);
            return $transactionId;

        } catch (Exception $ex) {
            error_log("✗ Transaction insertion error: " . $ex->getMessage());
            return false;
        }
    }
}