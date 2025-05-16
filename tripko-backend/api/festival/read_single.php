<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once(__DIR__ . '/../../config/db.php');

try {
    if (!isset($_GET['id'])) {
        throw new Exception('Missing festival ID');
    }

    $festival_id = intval($_GET['id']);

    $sql = "
        SELECT
            f.festival_id,
            f.name,
            f.description,
            f.date,
            f.image_path,
            f.town_id,
            t.town_name,
            COALESCE(f.status, 'active') as status
        FROM festivals f
        LEFT JOIN towns t ON f.town_id = t.town_id
        WHERE f.festival_id = ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $festival_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode([
            'success' => true,
            'festival' => $row
        ]);
    } else {
        throw new Exception('Festival not found');
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
