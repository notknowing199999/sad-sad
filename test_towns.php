<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = "localhost";
$username = "root";
$password = "";
$database = "tripko_db";

try {
    $conn = new mysqli($host, $username, $password, $database);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    echo "Connected successfully\n";
    
    $query = "SELECT COUNT(*) as count FROM towns";
    $result = $conn->query($query);
    
    if (!$result) {
        die("Query failed: " . $conn->error);
    }
    
    $row = $result->fetch_assoc();
    echo "Number of towns in database: " . $row['count'] . "\n";
    
    $conn->close();
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
