<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli('localhost', 'root', '', 'tripko_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . "\n");
}

// Check if town_id column exists in user table
echo "Checking user table structure:\n";
$result = $conn->query("SHOW COLUMNS FROM user LIKE 'town_id'");
$hasTownId = $result->num_rows > 0;

if (!$hasTownId) {
    echo "Adding town_id column to user table...\n";
    $alterQuery = "ALTER TABLE user ADD COLUMN town_id INT,
                  ADD FOREIGN KEY (town_id) REFERENCES towns(town_id)";
    if ($conn->query($alterQuery)) {
        echo "Added town_id column successfully\n";
    } else {
        echo "Error adding town_id column: " . $conn->error . "\n";
    }
}

// Check tourism officers and their town assignments
echo "\nChecking tourism officer town assignments:\n";
$query = "SELECT u.*, t.name as town_name 
          FROM user u 
          LEFT JOIN towns t ON u.town_id = t.town_id 
          WHERE u.user_type_id = 3";
$result = $conn->query($query);

if ($result) {
    while($row = $result->fetch_assoc()) {
        echo "Officer: {$row['username']}, Town: " . 
             ($row['town_name'] ? $row['town_name'] : 'No town assigned') . "\n";
    }
} else {
    echo "Error: " . $conn->error . "\n";
}

?>
