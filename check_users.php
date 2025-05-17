<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli('localhost', 'root', '', 'tripko_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "User table content:\n";
$result = $conn->query("SELECT u.*, ut.type_name, us.status_name 
                       FROM user u 
                       JOIN user_type ut ON u.user_type_id = ut.user_type_id 
                       JOIN user_status us ON u.user_status_id = us.user_status_id");

if ($result) {
    while($row = $result->fetch_assoc()) {
        echo "\nUser ID: {$row['user_id']}\n";
        echo "Username: {$row['username']}\n";
        echo "Type: {$row['type_name']} (ID: {$row['user_type_id']})\n";
        echo "Status: {$row['status_name']} (ID: {$row['user_status_id']})\n";
        echo "------------------------\n";
    }
} else {
    echo "Error: " . $conn->error;
}

?>
