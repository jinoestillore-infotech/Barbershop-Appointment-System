<?php
namespace Backend\Db;

class Database {
    public $conn;

    public function getConnection() {
        $this->conn = null;
        
        // Securely load configuration
        $config = require __DIR__ . '/../../config.php';
        
        try {
            // Create a new PDO instance using config file values
            $this->conn = new \PDO(
                "mysql:host=" . $config['db_host'] . ";dbname=" . $config['db_name'], 
                $config['db_user'], 
                $config['db_pass']
            );
            $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch(\PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>