<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('tripko-backend/config/Database.php');

$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    die("Database connection failed");
}

// Check table structure
$query = "SHOW COLUMNS FROM tourist_spots";
$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}

echo "Tourist Spots Table Structure:\n";
while ($row = $result->fetch_assoc()) {
    print_r($row);
    echo "\n";
}

// Check sample data
$query = "SELECT ts.*, t.name as town_name 
          FROM tourist_spots ts
          INNER JOIN towns t ON ts.town_id = t.town_id
          LIMIT 1";
$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}

echo "\nSample Tourist Spot Data:\n";
print_r($result->fetch_assoc());
?>
