<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli('localhost', 'root', '', 'tripko_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . "\n");
}

// Mapping of tourism officer usernames to their town IDs
$officerTowns = [
    'alaminos_tourism' => 1, // Assuming Alaminos is town_id 1
    'bolinao_tourism' => 2,  // Assuming Bolinao is town_id 2
    'bani_office' => 3       // Assuming Bani is town_id 3
];

foreach ($officerTowns as $username => $townId) {
    // First verify the town exists
    $townCheck = $conn->prepare("SELECT town_id FROM towns WHERE town_id = ?");
    $townCheck->bind_param("i", $townId);
    $townCheck->execute();
    $townResult = $townCheck->get_result();
    
    if ($townResult->num_rows > 0) {
        // Update the tourism officer's town_id
        $updateQuery = "UPDATE user SET town_id = ? WHERE username = ? AND user_type_id = 3";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("is", $townId, $username);
        
        if ($stmt->execute()) {
            echo "Updated town assignment for $username to town ID $townId\n";
        } else {
            echo "Failed to update town for $username: " . $stmt->error . "\n";
        }
    } else {
        echo "Town ID $townId not found in database\n";
    }
}

// Verify the updates
echo "\nVerifying tourism officer assignments:\n";
$query = "SELECT u.username, t.name as town_name 
          FROM user u 
          JOIN towns t ON u.town_id = t.town_id 
          WHERE u.user_type_id = 3";
$result = $conn->query($query);

if ($result) {
    while($row = $result->fetch_assoc()) {
        echo "{$row['username']} is assigned to {$row['town_name']}\n";
    }
} else {
    echo "Error verifying assignments: " . $conn->error . "\n";
}

?>
