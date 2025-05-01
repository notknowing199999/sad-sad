<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once(__DIR__ . '/../../config/db.php');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        exit;
    }

    $sql = "
        SELECT
            i.itinerary_id,
            i.name,
            i.description,
            i.destination_id,
            i.environmental_fee,
            i.image_path,
            t.town_name AS destination_name
        FROM itineraries AS i
        LEFT JOIN towns AS t ON i.destination_id = t.town_id
        ORDER BY i.itinerary_id DESC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    $records = [];
    while ($row = $result->fetch_assoc()) {
        $records[] = $row;
    }

    echo json_encode(['records' => $records]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}