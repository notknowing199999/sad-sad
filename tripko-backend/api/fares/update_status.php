<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../../config/db.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed');
    }

    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($data['fare_id']) || !isset($data['status'])) {
        throw new Exception('Missing required fields');
    }

    $fare_id = $data['fare_id'];
    $status = $data['status'];

    if (!in_array($status, ['active', 'inactive'])) {
        throw new Exception('Invalid status value');
    }

    $stmt = $conn->prepare("UPDATE fares SET status = ? WHERE fare_id = ?");
    $stmt->bind_param("si", $status, $fare_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception('Failed to update status');
    }

} catch (Exception $e) {
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
