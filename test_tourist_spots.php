<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('tripko-backend/config/Database.php');

$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    die("Database connection failed");
}

// Test query without status filter
$query = "SELECT ts.*, t.name as town_name 
          FROM tourist_spots ts
          INNER JOIN towns t ON ts.town_id = t.town_id
          ORDER BY ts.name ASC";

$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}

echo "Found " . $result->num_rows . " tourist spots:\n\n";

while ($row = $result->fetch_assoc()) {
    echo "ID: " . $row['spot_id'] . "\n";
    echo "Name: " . $row['name'] . "\n";
    echo "Town: " . $row['town_name'] . "\n";
    echo "Status: " . $row['status'] . "\n";
    echo "Image: " . $row['image_path'] . "\n";
    echo "-------------------\n";
}
?>
