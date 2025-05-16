<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . '/tripko-backend/config/db.php');

try {
    // Test the connection
    if (!$conn->ping()) {
        throw new Exception("Database connection lost");
    }
    
    // Check if towns table exists and get its structure
    $result = $conn->query("SHOW CREATE TABLE towns");
    if (!$result) {
        throw new Exception("Error checking towns table: " . $conn->error);
    }
    
    $row = $result->fetch_assoc();
    echo "Table structure:\n";
    echo $row['Create Table'] . "\n\n";
    
    // Try to insert a test record
    $name = "Test Town " . uniqid();
    $stmt = $conn->prepare("INSERT INTO towns (name, status) VALUES (?, 'active')");
    $stmt->bind_param("s", $name);
    
    if (!$stmt->execute()) {
        throw new Exception("Error inserting test record: " . $stmt->error);
    }
    
    $newId = $stmt->insert_id;
    echo "Successfully inserted test record with ID: $newId\n";
    
    // Clean up test record
    $stmt = $conn->prepare("DELETE FROM towns WHERE town_id = ?");
    $stmt->bind_param("i", $newId);
    $stmt->execute();
    
    echo "Successfully cleaned up test record\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
