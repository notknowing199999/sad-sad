<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . '/tripko-backend/config/db.php');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h2>Database Tables Status:</h2>";

$requiredTables = [
    'tourist_spots',
    'visitors_tracking',
    'transport_route',
    'route_terminals',
    'transportation_type',
    'route_transport_types',
    'user',
    'user_status'
];

$result = $conn->query("SHOW TABLES");
$existingTables = [];
while ($row = $result->fetch_array()) {
    $existingTables[] = $row[0];
}

foreach ($requiredTables as $table) {
    echo "<br>Checking table '$table': ";
    if (in_array($table, $existingTables)) {
        echo "EXISTS";
        
        // Check table structure
        $columns = $conn->query("SHOW COLUMNS FROM $table");
        echo "<br>Columns:<br>";
        while ($col = $columns->fetch_assoc()) {
            echo "- {$col['Field']} ({$col['Type']})<br>";
        }
        
        // Show row count
        $count = $conn->query("SELECT COUNT(*) as count FROM $table")->fetch_assoc();
        echo "Row count: {$count['count']}<br>";
    } else {
        echo "MISSING";
    }
    echo "<br>------------------------<br>";
}
?>