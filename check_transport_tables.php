<?php
require_once(__DIR__ . '/tripko-backend/config/db.php');

function checkTable($conn, $tableName) {
    $result = $conn->query("SELECT COUNT(*) as count FROM $tableName");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "$tableName has {$row['count']} records\n";
        
        // Get sample data
        $data = $conn->query("SELECT * FROM $tableName LIMIT 3");
        if ($data && $data->num_rows > 0) {
            echo "Sample data:\n";
            while ($row = $data->fetch_assoc()) {
                echo json_encode($row) . "\n";
            }
        }
    } else {
        echo "Error checking $tableName: " . $conn->error . "\n";
    }
    echo "\n";
}

echo "Checking transportation tables:\n\n";
checkTable($conn, "transportation_type");
checkTable($conn, "route_transport_types");
checkTable($conn, "transport_route");