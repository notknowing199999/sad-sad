<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('tripko-backend/config/Database.php');

$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    die("Database connection failed");
}

$username = 'bolinao_tourism';
$query = "SELECT u.*, t.name as town_name 
          FROM user u
          LEFT JOIN towns t ON u.town_id = t.town_id
          WHERE u.username = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo "Account found:\n";
    echo "Username: " . $row['username'] . "\n";
    echo "User Type: " . $row['user_type_id'] . "\n";
    echo "Town: " . $row['town_name'] . "\n";
    echo "Status: " . ($row['status'] ? 'Active' : 'Inactive') . "\n";
} else {
    echo "Account not found\n";
}
?>
