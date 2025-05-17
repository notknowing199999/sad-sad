<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli('localhost', 'root', '', 'tripko_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . "\n");
}

// First, get correct town IDs
$townIds = [];
$result = $conn->query("SELECT town_id, name FROM towns WHERE name IN ('Alaminos', 'Bolinao', 'Bani')");
while ($row = $result->fetch_assoc()) {
    $townIds[$row['name']] = $row['town_id'];
}

echo "Found town IDs:\n";
foreach ($townIds as $name => $id) {
    echo "$name: $id\n";
}

// Mapping of tourism officer usernames to their correct towns
$officerTowns = [
    'alaminos_tourism' => ['name' => 'Alaminos'],
    'bolinao_tourism' => ['name' => 'Bolinao'],
    'bani_office' => ['name' => 'Bani']
];

// Update each officer's town assignment
foreach ($officerTowns as $username => $townInfo) {
    $townId = $townIds[$townInfo['name']] ?? null;
    if ($townId) {
        $updateQuery = "UPDATE user SET town_id = ? WHERE username = ? AND user_type_id = 3";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("is", $townId, $username);
        
        if ($stmt->execute()) {
            echo "Updated town assignment for $username to {$townInfo['name']} (ID: $townId)\n";
        } else {
            echo "Failed to update town for $username: " . $stmt->error . "\n";
        }
    } else {
        echo "Could not find town ID for {$townInfo['name']}\n";
    }
}

// Verify the updates
echo "\nVerifying tourism officer assignments:\n";
$query = "SELECT u.username, t.name as town_name 
          FROM user u 
          JOIN towns t ON u.town_id = t.town_id 
          WHERE u.user_type_id = 3 
          ORDER BY u.username";
$result = $conn->query($query);

if ($result) {
    while($row = $result->fetch_assoc()) {
        echo "{$row['username']} is assigned to {$row['town_name']}\n";
    }
} else {
    echo "Error verifying assignments: " . $conn->error . "\n";
}

?>
