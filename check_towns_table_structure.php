<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . '/tripko-backend/config/db.php');

try {
    $result = $conn->query("DESCRIBE towns");
    if (!$result) {
        throw new Exception("Failed to get table structure: " . $conn->error);
    }

    echo "Table structure for 'towns':\n";
    echo str_repeat("-", 80) . "\n";
    echo sprintf("%-20s %-20s %-20s %-20s\n", "Field", "Type", "Null", "Key");
    echo str_repeat("-", 80) . "\n";
    
    while ($row = $result->fetch_assoc()) {
        echo sprintf("%-20s %-20s %-20s %-20s\n",
            $row['Field'],
            $row['Type'],
            $row['Null'],
            $row['Key']
        );
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
