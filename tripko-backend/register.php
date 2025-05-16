<?php
// tripko-backend/register.php

// Debug mode
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database connection
require_once __DIR__ . '/config/Database.php';

// Initialize database connection
$database = new Database();
$conn = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validate input
    if (empty($username) || empty($password)) {
        header('Location: ../tripko-frontend/file_html/SignUp_LogIn_Form.php?error=empty');
        exit();
    }

    try {
        // Check if username already exists
        $check_sql = "SELECT user_id FROM user WHERE username = :username";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bindParam(':username', $username);
        $check_stmt->execute();

        if ($check_stmt->rowCount() > 0) {
            header('Location: ../tripko-frontend/file_html/SignUp_LogIn_Form.php?error=exists');
            exit();
        }

        // Hash password and insert new user
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert new user with user_type_id = 2 (regular user) and active status
        $sql = "INSERT INTO user (username, password, user_type_id, user_status_id) VALUES (:username, :password, 2, 1)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashed_password);
        
        if ($stmt->execute()) {
            // Get the new user's ID
            $user_id = $conn->lastInsertId();
            
            // Create user profile
            $profile_sql = "INSERT INTO user_profile (user_id) VALUES (:user_id)";
            $profile_stmt = $conn->prepare($profile_sql);
            $profile_stmt->bindParam(':user_id', $user_id);
            $profile_stmt->execute();
            
            header('Location: ../tripko-frontend/file_html/SignUp_LogIn_Form.php?success=1');
            exit();
        } else {
            throw new Exception("Failed to create user");
        }
    } catch (PDOException $e) {
        error_log("Registration error: " . $e->getMessage());
        header('Location: ../tripko-frontend/file_html/SignUp_LogIn_Form.php?error=system');
        exit();
    }
} else {
    header('Location: ../tripko-frontend/file_html/SignUp_LogIn_Form.php');
    exit();
}
?>
