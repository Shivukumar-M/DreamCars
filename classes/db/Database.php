<?php

class Database {
    private static $dbInstance;

    private $db;

    public static function getInstance() {
        if(is_null(Database::$dbInstance)) {
            self::$dbInstance = new Database();
        }

        return self::$dbInstance;
    }

    private function __construct() {
    try {
        $this->db = new PDO(
            'mysql:host=localhost;dbname=carjack;charset=utf8',
            'root',   // MySQL username
            ''        // MySQL password (empty if none)
        );
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

    public function getDb() {
        return $this->db;
    }



}