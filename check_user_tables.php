<?php
require_once(__DIR__ . '/tripko-backend/config/db.php');

function checkTable($conn, $tableName) {
    echo "\nChecking table: $tableName\n";
    
    // Check if table exists
    $result = $conn->query("SHOW CREATE TABLE $tableName");
    if (!$result) {
        echo "Error: Table $tableName does not exist\n";
        return;
    }
    
    $row = $result->fetch_assoc();
    echo $row['Create Table'] . "\n";
    
    // Check row count
    $count = $conn->query("SELECT COUNT(*) as count FROM $tableName")->fetch_assoc();
    echo "Number of rows: " . $count['count'] . "\n";
}

// Check all user-related tables
$tables = ['user', 'user_profile', 'user_type', 'user_status'];
foreach ($tables as $table) {
    checkTable($conn, $table);
}
?>
