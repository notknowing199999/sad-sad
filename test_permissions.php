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

    echo "Testing database permissions...\n";
    $testName = "Test Town " . uniqid();
    $testImage = "test_" . uniqid() . ".jpg";
    
    $stmt = $conn->prepare("INSERT INTO towns (name, image_path, status) VALUES (?, ?, 'active')");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("ss", $testName, $testImage);
    if (!$stmt->execute()) {
        throw new Exception("Insert failed: " . $stmt->error);
    }
    
    $insertId = $stmt->insert_id;
    echo "Test record inserted successfully with ID: $insertId\n";
    
    // Clean up test record
    $conn->query("DELETE FROM towns WHERE town_id = $insertId");
    echo "Test record cleaned up\n\n";

    echo "Testing file upload directory...\n";
    $uploadDir = __DIR__ . '/uploads/towns/';
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0777, true)) {
            throw new Exception("Failed to create upload directory");
        }
        echo "Created upload directory: $uploadDir\n";
    }
    
    // Test file write permissions
    $testFile = $uploadDir . 'test.txt';
    if (!file_put_contents($testFile, 'test')) {
        throw new Exception("Failed to write test file");
    }
    echo "Successfully wrote test file\n";
    unlink($testFile);
    echo "Successfully removed test file\n";
    
    echo "\nAll tests passed successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
