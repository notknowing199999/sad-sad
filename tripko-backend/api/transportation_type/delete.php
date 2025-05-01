<?php
require_once(__DIR__ . '/../../config/db.php');
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid method']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$transport_type_id = $data['transport_type_id'] ?? null;

if (!$transport_type_id) {
    echo json_encode(['success' => false, 'message' => 'Missing ID']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM transportation_type WHERE transport_type_id=?");
$stmt->bind_param("i", $transport_type_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Delete failed']);
}