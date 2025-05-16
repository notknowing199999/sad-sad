<?php
$conn = new mysqli('localhost', 'root', '', 'tripko_db');
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Array of town images using local file paths
$townImages = [
    19 => ['name' => 'Dagupan', 'image' => '682450db1b567_dagupan-plaza.jpg'],
    22 => ['name' => 'Lingayen', 'image' => '682450db1c678_capitol.jpg'],
    33 => ['name' => 'San Fabian', 'image' => '682450db1d789_san-fabian-beach.jpg'],
    46 => ['name' => 'Urdaneta', 'image' => '682450db1e890_urdaneta-cathedral.jpg'],
    42 => ['name' => 'Sual', 'image' => '682450db1f901_sual-port.jpg']
];

// Update database entries
foreach ($townImages as $townId => $data) {
    try {
        $stmt = $conn->prepare("UPDATE towns SET image_path = ? WHERE town_id = ?");
        $stmt->bind_param("si", $data['image'], $townId);
        if (!$stmt->execute()) {
            throw new Exception("Failed to update database for {$data['name']}");
        }
        echo "Updated database entry for {$data['name']}\n";
        $stmt->close();
    } catch (Exception $e) {
        echo "Error updating {$data['name']}: " . $e->getMessage() . "\n";
    }
}

$conn->close();
echo "\nPlease add the following images to the uploads folder:\n";
foreach ($townImages as $data) {
    echo "{$data['image']}\n";
}
echo "\nImage names have been updated in the database.\n";
?>
