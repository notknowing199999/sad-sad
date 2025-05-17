<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../../config/db.php';

try {
    // Check user authorization
    session_start();
    $userType = $_SESSION['user_type'] ?? null;
    $userTownId = $_SESSION['town_id'] ?? null;

    if (!in_array($userType, ['admin', 'tourism_officer'])) {
        throw new Exception('Unauthorized access');
    }

    // Initialize database connection
    $database = new Database();
    $conn = $database->getConnection();
    
    if (!$conn) {
        throw new Exception("Database connection failed");
    }

    // Get posted data
    $data = $_POST;
    
    // Validate required fields
    if (!isset($data['spot_id'])) {
        throw new Exception('Missing spot ID');
    }

    // For tourism officers, verify they can only update spots in their town
    if ($userType === 'tourism_officer') {
        $check_query = "SELECT town_id FROM tourist_spots WHERE spot_id = ?";
        $check_stmt = $conn->prepare($check_query);
        if (!$check_stmt) {
            throw new Exception("Failed to prepare authorization check query");
        }
        
        $check_stmt->bind_param("i", $data['spot_id']);
        if (!$check_stmt->execute()) {
            throw new Exception("Failed to execute authorization check");
        }
        
        $result = $check_stmt->get_result();
        $spot = $result->fetch_assoc();
        
        if (!$spot || $spot['town_id'] != $userTownId) {
            throw new Exception('You can only update tourist spots in your assigned municipality');
        }
    }

    // Initialize tourist spot object
    $tourist_spot = new TouristSpot($conn);
    
    // Handle image upload if present
    $image_path = null;
    if (isset($_FILES['images']) && $_FILES['images']['error'][0] === UPLOAD_ERR_OK) {
        $uploadDir = "../../../uploads/";
        if (!file_exists($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
                throw new Exception("Failed to create upload directory");
            }
        }
        
        $filename = uniqid() . '_' . basename($_FILES['images']['name'][0]);
        $targetFile = $uploadDir . $filename;
        
        if (!move_uploaded_file($_FILES['images']['tmp_name'][0], $targetFile)) {
            throw new Exception("Failed to upload image");
        }
        
        $image_path = $filename;
        $data['image_path'] = $image_path;
    }

    // Validate status if being updated
    if (isset($data['status']) && !in_array($data['status'], ['active', 'inactive'])) {
        throw new Exception('Invalid status value');
    }

    // Update the tourist spot
    $success = $tourist_spot->update($data);
    
    if (!$success) {
        throw new Exception("Failed to update tourist spot");
    }

    http_response_code(200);
    echo json_encode(array(
        'success' => true,
        'message' => 'Tourist spot updated successfully'
    ));

} catch (Exception $e) {
    error_log("Error in update tourist spot API: " . $e->getMessage());
    
    $statusCode = 500;
    if (strpos($e->getMessage(), 'Unauthorized') !== false) {
        $statusCode = 403;
    } elseif ($e->getMessage() === 'Missing spot ID') {
        $statusCode = 400;
    }
    
    http_response_code($statusCode);
    echo json_encode(array(
        'success' => false,
        'message' => $e->getMessage()
    ));
}
?>