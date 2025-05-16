<?php
$conn = new mysqli('localhost', 'root', '', 'tripko_db');
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Array of town images with their corresponding landmarks/destinations
$townImages = [
    2 => ['name' => 'Aguilar', 'image' => '682450db1a234_aguilar-plaza.jpg', 'url' => 'https://scontent.fmnl9-2.fna.fbcdn.net/v/t1.6435-9/67895878_2324651161126543_261032461277184000_n.jpg'],
    4 => ['name' => 'Alcala', 'image' => '682450db1a345_alcala-church.jpg', 'url' => 'https://alcala.gov.ph/wp-content/uploads/2023/03/20230315_075350-scaled.jpg'],
    5 => ['name' => 'Anda', 'image' => '682450db1a456_tondol-beach.jpg', 'url' => 'https://live.staticflickr.com/65535/48976615908_486b2c7e4d_b.jpg'],
    19 => ['name' => 'Dagupan', 'image' => '682450db1b567_dagupan-plaza.jpg', 'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5c/Dagupan_City_Plaza.jpg/1200px-Dagupan_City_Plaza.jpg'],
    22 => ['name' => 'Lingayen', 'image' => '682450db1c678_capitol.jpg', 'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b4/Pangasinan_Provincial_Capitol.jpg/1200px-Pangasinan_Provincial_Capitol.jpg'],
    // Add more towns here with their images
];

$uploadsDir = __DIR__ . '/uploads/';
if (!is_dir($uploadsDir)) {
    mkdir($uploadsDir, 0777, true);
}

// Download and save images
foreach ($townImages as $townId => $data) {
    try {
        // Skip if image already exists
        if (file_exists($uploadsDir . $data['image'])) {
            echo "Image for {$data['name']} already exists\n";
            continue;
        }

        // Download image
        $imageContent = @file_get_contents($data['url']);
        if ($imageContent === false) {
            throw new Exception("Failed to download image for {$data['name']}");
        }

        // Save image
        if (!file_put_contents($uploadsDir . $data['image'], $imageContent)) {
            throw new Exception("Failed to save image for {$data['name']}");
        }

        // Update database
        $stmt = $conn->prepare("UPDATE towns SET image_path = ? WHERE town_id = ?");
        $stmt->bind_param("si", $data['image'], $townId);
        if (!$stmt->execute()) {
            throw new Exception("Failed to update database for {$data['name']}");
        }

        echo "Successfully processed {$data['name']}\n";
        $stmt->close();

    } catch (Exception $e) {
        echo "Error processing {$data['name']}: " . $e->getMessage() . "\n";
    }
}

$conn->close();
echo "Image update process completed\n";
?>
