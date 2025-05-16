<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once '../../config/Database.php';
include_once '../../models/TouristSpot.php';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize TouristSpot object
$tourist_spot = new TouristSpot($db);

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        header('HTTP/1.1 405 Method Not Allowed');
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        exit;
    }    
    // Get tourist spots
    $result = $tourist_spot->read();
    
    if($result && $result->num_rows > 0) {
        $spots_arr = array();
        $spots_arr['records'] = array();

        while($row = $result->fetch_assoc()) {
            $spot_item = array(
                'spot_id' => $row['spot_id'],
                'name' => $row['name'],
                'description' => $row['description'],
                'town_id' => $row['town_id'],
                'town_name' => $row['town_name'] ?? '',
                'status' => $row['status'],
                'image_path' => $row['image_path'],
                'category' => $row['category'],
                'contact_info' => $row['contact_info']
            );

            array_push($spots_arr['records'], $spot_item);
        }

        header('HTTP/1.1 200 OK');
        echo json_encode($spots_arr);
    } else {
        header('HTTP/1.1 404 Not Found');
        echo json_encode(array('message' => 'No tourist spots found'));
    }
} catch(Exception $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(array(
        'message' => 'Unable to fetch tourist spots',
        'error' => $e->getMessage()
     ));
}