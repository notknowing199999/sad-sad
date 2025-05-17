<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli('localhost', 'root', '', 'tripko_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get admin's current password hash
$result = $conn->query("SELECT password FROM user WHERE username = 'admin'");
if ($result) {
    $admin = $result->fetch_assoc();
    if ($admin) {
        echo "Current admin password hash: " . $admin['password'] . "\n";
        
        // Test if it matches 'admin123'
        $isValid = password_verify('admin123', $admin['password']);
        echo "Password 'admin123' is " . ($isValid ? "valid" : "invalid") . "\n";
        
        if (!$isValid) {
            // Reset admin password to admin123
            $newHash = password_hash('admin123', PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE user SET password = ? WHERE username = 'admin'");
            $stmt->bind_param('s', $newHash);
            if ($stmt->execute()) {
                echo "Admin password has been reset to 'admin123'\n";
            } else {
                echo "Failed to reset admin password\n";
            }
        }
    } else {
        echo "Admin user not found\n";
    }
} else {
    echo "Error: " . $conn->error;
}
?>
