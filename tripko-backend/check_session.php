<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function checkSession($required_type = null) {
    // Enhanced security check for session fixation
    if (!isset($_SESSION['initialized'])) {
        session_regenerate_id(true);
        $_SESSION['initialized'] = true;
    }

    // Check if session exists and has required data
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type_id']) || !isset($_SESSION['username'])) {
        session_destroy();
        header("Location: ../tripko-frontend/file_html/SignUp_LogIn_Form.php?error=session");
        exit();
    }

    // Check session expiration with grace period
    if (!isset($_SESSION['expires']) || time() > $_SESSION['expires']) {
        session_destroy();
        header("Location: ../tripko-frontend/file_html/SignUp_LogIn_Form.php?error=timeout");
        exit();
    }

    // Refresh session expiration on activity
    $_SESSION['expires'] = time() + (2 * 60 * 60); // 2 hours

    // If specific user type is required
    if ($required_type !== null) {
        if ($_SESSION['user_type_id'] != $required_type) {
            if ($_SESSION['user_type_id'] == 1) {
                header("Location: ../tripko-frontend/file_html/dashboard.php");
            } else {
                header("Location: ../tripko-frontend/file_html/homepage.php");
            }
            exit();
        }
    }

    // Extend session timeout
    $_SESSION['expires'] = time() + (2 * 60 * 60);
}

function checkAdminSession() {
    checkSession(1); // 1 is admin type
}

function checkUserSession() {
    checkSession(2); // 2 is regular user type
}

function isAdmin() {
    return isset($_SESSION['user_type_id']) && $_SESSION['user_type_id'] == 1;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isTourismOfficer() {
    return isset($_SESSION['user_type_id']) && $_SESSION['user_type_id'] == 3;
}

function checkTourismOfficerSession() {
    checkSession(3); // 3 is tourism officer type
    
    // Verify town_id is set for tourism officers
    if (!isset($_SESSION['town_id'])) {
        $database = new Database();
        $conn = $database->getConnection();
        
        if ($conn) {
            // Get town assignment from user table
            $query = "SELECT u.town_id, t.name as town_name 
                     FROM user u
                     LEFT JOIN towns t ON u.town_id = t.town_id 
                     WHERE u.user_id = ? AND u.user_type_id = 3";
            $stmt = $conn->prepare($query);
            if ($stmt) {
                $stmt->bind_param("i", $_SESSION['user_id']);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    if ($row['town_id']) {
                        $_SESSION['town_id'] = $row['town_id'];
                        $_SESSION['town_name'] = $row['town_name'];
                    } else {
                        session_destroy();
                        header("Location: ../tripko-frontend/file_html/SignUp_LogIn_Form.php?error=no_town");
                        exit();
                    }
                }
                $stmt->close();
            }
        }
        
        if (!isset($_SESSION['town_id'])) {
            session_destroy();
            header("Location: ../tripko-frontend/file_html/SignUp_LogIn_Form.php?error=no_town");
            exit();
        }
    }
}
?>