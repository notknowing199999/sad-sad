<?php
class Database {
    private $host = "localhost";
    private $db_name = "tripko_db";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);
            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }
            if (!$this->conn->set_charset("utf8mb4")) {
                throw new Exception("Error setting charset utf8mb4: " . $this->conn->error);
            }
            
            // Test the connection
            if (!$this->conn->ping()) {
                throw new Exception("Error: Lost connection to MySQL server");
            }
            
            return $this->conn;
        } catch(Exception $e) {
            error_log("Database connection error: " . $e->getMessage());
            return null;
        }
    }
}
?>
