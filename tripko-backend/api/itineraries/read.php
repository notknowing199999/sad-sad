<?php
// Prevent any output before headers
ob_start();

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

session_start();
require_once(__DIR__ . '/../../config/check_session.php');

if (!isset($_SESSION['user_id'])) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

include_once '../../config/Database.php';
include_once '../../models/Itinerary.php';

$database = new Database();
$db = $database->getConnection();
$itinerary = new Itinerary($db);

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        header('HTTP/1.1 405 Method Not Allowed');
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        exit;
    }

    $result = $itinerary->read();
    
    if($result && $result->num_rows > 0) {
        $itineraries_arr = array();
        $itineraries_arr['records'] = array();

        while($row = $result->fetch_assoc()) {
            $itinerary_item = array(
                'id' => $row['itinerary_id'],
                'name' => $row['name'],
                'description' => $row['description'],
                'destination' => $row['town_name'],
                'destination_id' => $row['town_id'],
                'environmental_fee' => $row['environmental_fee'],
                'image_path' => $row['image_path'],
                'status' => $row['status'],
                'duration' => $row['duration'],
                'tourist_spots' => $row['tourist_spots'],
                'budget' => $row['budget']
            );
            array_push($itineraries_arr['records'], $itinerary_item);
        }

        header('HTTP/1.1 200 OK');
        ob_end_clean();
        echo json_encode($itineraries_arr);
    } else {
        header('HTTP/1.1 404 Not Found');
        ob_end_clean();
        echo json_encode(['message' => 'No itineraries found']);
    }
} catch(Exception $e) {
    error_log("Error in read.php: " . $e->getMessage());
    header('HTTP/1.1 500 Internal Server Error');
    ob_end_clean();
    echo json_encode([
        'message' => 'Unable to fetch itineraries',
        'error' => $e->getMessage()
    ]);
}