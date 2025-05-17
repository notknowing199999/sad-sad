<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: DELETE');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods,Authorization,X-Requested-With');

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

    // Initialize database connection
    $database = new Database();
    $conn = $database->getConnection();
    
    if (!$conn) {
        throw new Exception("Database connection failed", 500);
    }

    // Get spot ID to delete
    $spot_id = isset($_GET['spot_id']) ? intval($_GET['spot_id']) : null;
    
    if (!$spot_id) {
        throw new Exception('Missing or invalid spot ID', 400);
    }

    // For tourism officers, verify they can only delete spots in their town
    if ($userType === 'tourism_officer') {
        $check_query = "SELECT town_id, image_path FROM tourist_spots WHERE spot_id = ?";
        $check_stmt = $conn->prepare($check_query);
        if (!$check_stmt) {
            throw new Exception("Failed to prepare authorization check query", 500);
        }
        
        $check_stmt->bind_param("i", $spot_id);
        if (!$check_stmt->execute()) {
            throw new Exception("Failed to execute authorization check", 500);
        }
        
        $result = $check_stmt->get_result();
        $spot = $result->fetch_assoc();
        
        if (!$spot) {
            throw new Exception('Tourist spot not found', 404);
        }
        
        if ($spot['town_id'] != $userTownId) {
            throw new Exception('You can only delete tourist spots in your assigned municipality', 403);
        }
    }

    // Initialize tourist spot object
    $tourist_spot = new TouristSpot($conn);
    
    // Get image path before deletion
    $img_query = "SELECT image_path FROM tourist_spots WHERE spot_id = ?";
    $img_stmt = $conn->prepare($img_query);
    $img_stmt->bind_param("i", $spot_id);
    $img_stmt->execute();
    $image_result = $img_stmt->get_result();
    $image_data = $image_result->fetch_assoc();
    $image_path = $image_data['image_path'] ?? null;

    // Delete the tourist spot
    if (!$tourist_spot->delete($spot_id)) {
        throw new Exception("Failed to delete tourist spot", 500);
    }

    // Delete associated image if it exists
    if ($image_path) {
        $file_path = "../../../uploads/" . $image_path;
        if (file_exists($file_path)) {
            if (!unlink($file_path)) {
                error_log("Warning: Failed to delete image file: " . $file_path);
                // Don't throw exception as the spot was successfully deleted
            }
        }
    }

    http_response_code(200);
    echo json_encode(array(
        'success' => true,
        'message' => 'Tourist spot deleted successfully'
    ));

} catch (Exception $e) {
    error_log("Error in delete tourist spot API: " . $e->getMessage());
    
    $statusCode = $e->getCode() ?: 500;
    http_response_code($statusCode);
    
    echo json_encode(array(
        'success' => false,
        'message' => $e->getMessage()
    ));
}
?>