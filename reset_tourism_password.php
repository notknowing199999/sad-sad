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
$new_password = 'tourism2024'; // Default password
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

$query = "UPDATE user SET password = ? WHERE username = ? AND user_type_id = 3";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $hashed_password, $username);

if ($stmt->execute()) {
    echo "Password reset successful!\n";
    echo "Username: " . $username . "\n";
    echo "New password: " . $new_password . "\n";
} else {
    echo "Error resetting password: " . $stmt->error . "\n";
}
?>
