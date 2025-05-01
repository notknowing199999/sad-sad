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
            s.spot_id,
            s.name,
            s.description,
            s.town_id,
            s.category,
            s.contact_info,
            s.image_path,
            t.town_name
        FROM tourist_spots AS s
        LEFT JOIN towns AS t ON s.town_id = t.town_id
        ORDER BY s.spot_id DESC
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