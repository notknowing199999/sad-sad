<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli('localhost', 'root', '', 'tripko_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check user_type table
echo "\nUser Types:\n";
$result = $conn->query("SELECT * FROM user_type");
if ($result) {
    while($row = $result->fetch_assoc()) {
        echo "{$row['user_type_id']}: {$row['type_name']}\n";
    }
} else {
    echo "Error querying user_type: " . $conn->error . "\n";
}

// Check admin user
echo "\nAdmin User:\n";
$result = $conn->query("SELECT u.*, ut.type_name, us.status_name FROM user u 
                       JOIN user_type ut ON u.user_type_id = ut.user_type_id 
                       JOIN user_status us ON u.user_status_id = us.user_status_id 
                       WHERE u.username = 'admin'");
if ($result) {
    $admin = $result->fetch_assoc();
    if ($admin) {
        echo "Username: {$admin['username']}\n";
        echo "Type: {$admin['type_name']}\n";
        echo "Status: {$admin['status_name']}\n";
    } else {
        echo "Admin user not found\n";
    }
} else {
    echo "Error querying admin: " . $conn->error . "\n";
}

// Check user_status table
echo "\nUser Statuses:\n";
$result = $conn->query("SELECT * FROM user_status");
if ($result) {
    while($row = $result->fetch_assoc()) {
        echo "{$row['user_status_id']}: {$row['status_name']}\n";
    }
} else {
    echo "Error querying user_status: " . $conn->error . "\n";
}

?>
