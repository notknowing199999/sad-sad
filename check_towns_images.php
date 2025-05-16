<?php
$conn = new mysqli('localhost', 'root', '', 'tripko_db');
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

$result = $conn->query('SELECT town_id, name, image_path FROM towns ORDER BY name');
while ($row = $result->fetch_assoc()) {
    echo $row['town_id'] . ': ' . $row['name'] . ' - ' . ($row['image_path'] ?? 'NULL') . "\n";
}
$conn->close();
?>
