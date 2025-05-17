<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../../config/Database.php';
require_once '../../models/TouristSpot.php';
require_once '../../config/check_session.php';

try {
    // Check user authorization
    session_start();
    $userType = $_SESSION['user_type'] ?? null;
    $userTownId = $_SESSION['town_id'] ?? null;

    if (!in_array($userType, ['admin', 'tourism_officer'])) {
        throw new Exception('Unauthorized access', 403);
    }

    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($data['spot_id']) || !isset($data['status'])) {
        throw new Exception('Missing required fields', 400);
    }

    if (!in_array($data['status'], ['active', 'inactive'])) {
        throw new Exception('Invalid status value', 400);
    }

    // Initialize database connection
    $database = new Database();
    $conn = $database->getConnection();
    
    // For tourism officers, verify they can only update spots in their town
    if ($userType === 'tourism_officer') {
        $check_query = "SELECT town_id FROM tourist_spots WHERE spot_id = ?";
        $check_stmt = $conn->prepare($check_query);
        if (!$check_stmt) {
            throw new Exception("Failed to prepare authorization check query", 500);
        }
        
        $check_stmt->bind_param("i", $data['spot_id']);
        if (!$check_stmt->execute()) {
            throw new Exception("Failed to execute authorization check", 500);
        }
        
        $result = $check_stmt->get_result();
        $spot = $result->fetch_assoc();
        
        if (!$spot) {
            throw new Exception('Tourist spot not found', 404);
        }
        
        if ($spot['town_id'] != $userTownId) {
            throw new Exception('You can only update tourist spots in your assigned municipality', 403);
        }
    }

    // Initialize tourist spot object and update status
    $tourist_spot = new TouristSpot($conn);
    $success = $tourist_spot->updateStatus($data['spot_id'], $data['status']);
    
    if (!$success) {
        throw new Exception("Failed to update tourist spot status", 500);
    }

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Tourist spot status updated successfully'
    ]);

} catch (Exception $e) {
    error_log("Error in update status API: " . $e->getMessage());
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
