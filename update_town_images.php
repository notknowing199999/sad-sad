<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = "localhost";
$username = "root";
$password = "";
$database = "tripko_db";

// Array of town images to download and associate
$townImages = [
    1 => ['name' => 'Agno', 'image' => '6820d5c10ded6_agnoooo.jpg'],
    3 => ['name' => 'Alaminos', 'image' => '6813945d4ac34_hundred-island.jpg'],
    14 => ['name' => 'Bolinao', 'image' => '681394705bcd7_bolinao3.jpg'],
    // Add more towns and their images here as we collect them
];

try {
    // Connect to database
    $conn = new mysqli($host, $username, $password, $database);
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Update each town's image
    foreach ($townImages as $townId => $data) {
        $sql = "UPDATE towns SET image_path = ? WHERE town_id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $conn->error);
        }

        $stmt->bind_param("si", $data['image'], $townId);
        if (!$stmt->execute()) {
            throw new Exception("Failed to update town {$data['name']}: " . $stmt->error);
        }

        echo "Updated image for {$data['name']}\n";
        $stmt->close();
    }

    $conn->close();
    echo "Successfully updated town images\n";

} catch (Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}
?>
