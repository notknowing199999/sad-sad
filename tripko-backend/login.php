<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$dbname = 'tripko_db';
$dbuser = 'root';
$dbpass = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $dbuser, $dbpass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = trim($_POST['username']);
        $password = $_POST['password'];

        // Debug output
        error_log("Login attempt - Username: " . $username);

        $sql = "SELECT * FROM user WHERE username = :username";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_type_id'] = $user['user_type_id'];

            error_log("Login successful - User Type: " . $user['user_type_id']);

            if ($user['user_type_id'] == 1) {
                header("Location: ../tripko-frontend/file_html/dashboard.php");
                exit();
            } else {
                header("Location: ../tripko-frontend/file_html/homepage.php");
                exit();
            }
        } else {
            error_log("Login failed - Invalid credentials");
            header("Location: ../tripko-frontend/file_html/SignUp_LogIn_Form.php?error=invalid");
            exit();
        }
    }
} catch(PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    header("Location: ../tripko-frontend/file_html/SignUp_LogIn_Form.php?error=system");
    exit();
}
?>