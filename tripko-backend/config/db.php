<?php
// db.php

$servername = "localhost";   // usually localhost
$username = "root";           // your database username
$password = "";               // your database password
$dbname = "tripko_db";    // your database name

// Create connection with improved error handling
try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset to utf8mb4
    if (!$conn->set_charset("utf8mb4")) {
        throw new Exception("Error setting charset utf8mb4: " . $conn->error);
    }
    
    // Test the connection
    if (!$conn->ping()) {
        throw new Exception("Error: Lost connection to MySQL server");
    }
} catch (Exception $e) {
    // Log the error and re-throw
    error_log("Database connection error: " . $e->getMessage());
    throw $e;
}

?>
