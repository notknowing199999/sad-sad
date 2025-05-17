<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('tripko-backend/config/Database.php');

$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    die("Database connection failed");
}

$sql = "
ALTER TABLE tourist_spots 
ADD COLUMN IF NOT EXISTS location VARCHAR(255),
ADD COLUMN IF NOT EXISTS operating_hours VARCHAR(255),
ADD COLUMN IF NOT EXISTS entrance_fee VARCHAR(100),
MODIFY COLUMN category VARCHAR(100) NULL,
MODIFY COLUMN name VARCHAR(150) NULL;
";

if ($conn->multi_query($sql)) {
    echo "Table structure updated successfully\n";
} else {
    echo "Error updating table: " . $conn->error . "\n";
}

// Verify the changes
$query = "SHOW COLUMNS FROM tourist_spots";
$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}

echo "\nUpdated Tourist Spots Table Structure:\n";
while ($row = $result->fetch_assoc()) {
    print_r($row);
    echo "\n";
}
?>
