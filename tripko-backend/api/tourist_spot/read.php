<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../../config/Database.php';
require_once '../../models/TouristSpot.php';

try {
    // Initialize database connection
    $database = new Database();
    $conn = $database->getConnection();
    
    if (!$conn) {
        throw new Exception("Database connection failed");
    }

    // Initialize tourist spot object
    $tourist_spot = new TouristSpot($conn);
    
    // Get the user type from the session if available
    session_start();
    $userType = $_SESSION['user_type'] ?? null;
    
    // Get tourist spots based on user type
    if ($userType === 'admin' || $userType === 'tourism_officer') {
        $result = $tourist_spot->read();
    } else {
        // Regular users only see active spots
        $result = $tourist_spot->readActive();
    }
    
    if (!$result) {
        throw new Exception("Failed to fetch tourist spots");
    }
    
    // Get number of rows
    $num = $result->num_rows;
    
    if ($num > 0) {
        $spots_arr = array();
        $spots_arr['records'] = array();
        
        while ($row = $result->fetch_assoc()) {
            $spot_item = array(
                'spot_id' => $row['spot_id'],
                'name' => $row['name'],
                'description' => $row['description'],
                'category' => $row['category'],
                'town_id' => $row['town_id'],
                'town_name' => $row['town_name'],
                'contact_info' => $row['contact_info'],
                'image_path' => $row['image_path'],
                'status' => $row['status'] ?? 'active',
                'created_at' => $row['created_at'],
                'updated_at' => $row['updated_at']
            );
            array_push($spots_arr['records'], $spot_item);
        }
        
        http_response_code(200);
        $spots_arr['success'] = true;
        echo json_encode($spots_arr);
    } else {
        http_response_code(200);
        echo json_encode(array(
            'success' => true,
            'message' => 'No tourist spots found',
            'records' => array()
        ));
    }
} catch (Exception $e) {
    error_log("Error in tourist spots API: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(array(
        'success' => false,
        'message' => 'An error occurred while fetching tourist spots. Please try again later.',
        'error' => $e->getMessage()
    ));
}