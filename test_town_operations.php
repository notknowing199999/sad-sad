<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . '/tripko-backend/config/db.php');

try {
    echo "Testing database connection...\n";
    if (!$conn->ping()) {
        throw new Exception("Database connection failed");
    }
    echo "Database connection successful\n\n";

    // Test insert
    $testName = "Test Town " . uniqid();
    echo "Testing insert of town: $testName\n";
    
    $stmt = $conn->prepare("INSERT INTO towns (name, status) VALUES (?, 'active')");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("s", $testName);
    if (!$stmt->execute()) {
        throw new Exception("Insert failed: " . $stmt->error);
    }
    
    $insertId = $stmt->insert_id;
    echo "Test record inserted successfully with ID: $insertId\n\n";

    // Test update with image
    echo "Testing update with image...\n";
    $testImage = "test_" . uniqid() . ".txt";
    $uploadDir = __DIR__ . '/uploads/towns/';
    $targetFile = $uploadDir . $testImage;
    
    // Create test file
    if (!file_put_contents($targetFile, "test content")) {
        throw new Exception("Failed to create test file");
    }
    echo "Test file created: $targetFile\n";

    // Update database with image path
    $stmt = $conn->prepare("UPDATE towns SET image_path = ? WHERE town_id = ?");
    if (!$stmt->execute([$testImage, $insertId])) {
        throw new Exception("Update failed: " . $stmt->error);
    }
    echo "Database updated with image path\n";

    // Verify update
    $stmt = $conn->prepare("SELECT name, image_path FROM towns WHERE town_id = ?");
    $stmt->bind_param("i", $insertId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    echo "\nVerifying record:\n";
    echo "Name: " . $row['name'] . "\n";
    echo "Image path: " . $row['image_path'] . "\n\n";

    // Clean up
    unlink($targetFile);
    $conn->query("DELETE FROM towns WHERE town_id = $insertId");
    echo "Test records cleaned up\n";
    
    echo "\nAll tests completed successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
