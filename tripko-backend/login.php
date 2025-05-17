<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/Database.php';

// Initialize database connection
$database = new Database();
$conn = $database->getConnection();

// Check if connection was successful
if (!$conn) {
    error_log("Failed to connect to database");
    header("Location: ../tripko-frontend/file_html/SignUp_LogIn_Form.php?error=connection");
    exit();
}

try {
    error_log("Login attempt starting");
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        error_log("POST data received - Username: " . ($_POST['username'] ?? 'not set'));
        // Rate limiting check
        if (isset($_SESSION['login_attempts']) && $_SESSION['login_attempts'] >= 5 && 
            time() - ($_SESSION['last_attempt'] ?? 0) < 300) {
            header("Location: ../tripko-frontend/file_html/SignUp_LogIn_Form.php?error=too_many_attempts");
            exit();
        }

        // Input validation
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password) || strlen($username) > 100 || strlen($password) < 6) {
            $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
            $_SESSION['last_attempt'] = time();
            header("Location: ../tripko-frontend/file_html/SignUp_LogIn_Form.php?error=invalid_input");
            exit();
        }

        // Sanitize username
        $username = $conn->real_escape_string($username);

        // Prepare and execute query
        // Get user data including town_id for tourism officers
        $sql = "SELECT u.*, ut.type_name, us.status_name
                FROM user u 
                JOIN user_type ut ON u.user_type_id = ut.user_type_id 
                JOIN user_status us ON u.user_status_id = us.user_status_id 
                WHERE u.username = ?";
                
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("s", $username);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        error_log("User query result: " . ($user ? "User found" : "User not found"));
        if ($user) {
            error_log("Password verify result: " . (password_verify($password, $user['password']) ? "true" : "false"));
        }

        if ($user && password_verify($password, $user['password'])) {
            // Check if user is active
            if ($user['status_name'] !== 'Active') {
                header("Location: ../tripko-frontend/file_html/SignUp_LogIn_Form.php?error=inactive");
                exit();
            }

            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_type_id'] = $user['user_type_id'];
            $_SESSION['user_type'] = $user['type_name'];

            // For tourism officers, get and verify their assigned town
            if ($user['user_type_id'] == 3) {
                // Get town assignment directly from user table
                $town_query = "SELECT u.town_id, t.name as town_name 
                               FROM user u
                               INNER JOIN towns t ON u.town_id = t.town_id
                               WHERE u.user_id = ? AND u.user_type_id = 3";
                $town_stmt = $conn->prepare($town_query);
                if (!$town_stmt) {
                    throw new Exception("Failed to prepare town query: " . $conn->error);
                }
                
                $town_stmt->bind_param("i", $user['user_id']);
                if (!$town_stmt->execute()) {
                    throw new Exception("Failed to check town assignment: " . $town_stmt->error);
                }
                
                $town_result = $town_stmt->get_result();
                $town_data = $town_result->fetch_assoc();
                
                if (!$town_data || !$town_data['town_id']) {
                    $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
                    $_SESSION['last_attempt'] = time();
                    header("Location: ../tripko-frontend/file_html/SignUp_LogIn_Form.php?error=no_town");
                    exit();
                }
                
                $_SESSION['town_id'] = $town_data['town_id'];
                $_SESSION['town_name'] = $town_data['town_name'];
                error_log("Set town_id in session: " . $_SESSION['town_id']);
                error_log("Set town_name in session: " . $_SESSION['town_name']);
            }

            // Regenerate session ID for security
            session_regenerate_id(true);

            // Set session timeout to 2 hours
            $_SESSION['expires'] = time() + (2 * 60 * 60);

            // Clear login attempts on successful login
            unset($_SESSION['login_attempts']);
            unset($_SESSION['last_attempt']);

            // Redirect based on user type
            switch ($user['user_type_id']) {
                case 1: // Admin
                    header("Location: ../tripko-frontend/file_html/dashboard.php");
                    break;
                case 3: // Tourism Officer
                    header("Location: ../tripko-frontend/file_html/tourism_offices/dashboard.php");
                    break;
                default: // Regular users
                    header("Location: ../tripko-frontend/file_html/homepage.php");
            }
            exit();
        } else {
            $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
            $_SESSION['last_attempt'] = time();
            error_log("Login failed - Invalid credentials for user: " . $username);
            header("Location: ../tripko-frontend/file_html/SignUp_LogIn_Form.php?error=invalid");
            exit();
        }

        $stmt->close();
    }
} catch(Exception $e) {
    $error_details = "Login error: " . $e->getMessage() . 
                  "\nStack trace: " . $e->getTraceAsString() . 
                  "\nFile: " . $e->getFile() . " Line: " . $e->getLine();
    error_log($error_details);
    header("Location: ../tripko-frontend/file_html/SignUp_LogIn_Form.php?error=system");
    exit();
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>