<?php
// tripko-backend/register.php

// 1) Debug mode on
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2) Include your DB connection
require_once __DIR__ . '/config/Database.php';

// 3) Only handle POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../tripko-frontend/SignUp_LogIn_Form.php');
    exit();
}

// 4) Grab & validate
$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($username === '' || $password === '') {
    // missing fields → back with error flag
    header('Location: ../tripko-frontend/SignUp_LogIn_Form.php?error=empty');
    exit();
}

try {
    // 5) Hash & prepare
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO user (username, password) VALUES (:username, :password)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $hashed);

    // **This was missing**! Actually run the query:
    $stmt->execute();

    // 6) Redirect back to login with success flag
    header('Location: ../tripko-frontend/SignUp_LogIn_Form.php?registered=1');
    exit();

} catch (PDOException $e) {
    // log it for yourself
    error_log('Registration error: ' . $e->getMessage());

    // 7) Redirect back with a generic error
    header('Location: ../tripko-frontend/SignUp_LogIn_Form.php?error=system');
    exit();
}
