<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli('localhost', 'root', '', 'tripko_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . "\n");
}

echo "User Type table content:\n";
$result = $conn->query("SELECT * FROM user_type");

if ($result) {
    while($row = $result->fetch_assoc()) {
        echo "ID: {$row['user_type_id']}, Name: {$row['type_name']}\n";
    }
} else {
    echo "Error: " . $conn->error . "\n";
}

echo "\nChecking type_name for user_type_id = 3:\n";
$result = $conn->query("SELECT type_name FROM user_type WHERE user_type_id = 3");
if ($result) {
    $row = $result->fetch_assoc();
    if ($row) {
        echo "Tourism officer type name is: {$row['type_name']}\n";
        
        // Update the type name if it's not correct
        if ($row['type_name'] !== 'tourism_officer') {
            $stmt = $conn->prepare("UPDATE user_type SET type_name = 'tourism_officer' WHERE user_type_id = 3");
            if ($stmt->execute()) {
                echo "Updated type_name for tourism officers to 'tourism_officer'\n";
            } else {
                echo "Failed to update type_name: " . $stmt->error . "\n";
            }
        }
    } else {
        echo "No type found with ID 3\n";
    }
} else {
    echo "Error: " . $conn->error . "\n";
}

?>
