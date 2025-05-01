<?php
require_once(__DIR__ . '/../../config/db.php');
header("Content-Type: application/json; charset=UTF-8");

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['terminal_id'] ?? '';

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Missing terminal_id']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM route_terminals WHERE terminal_id=?");
$stmt->bind_param("i", $id);
$stmt->execute();

echo json_encode(['success' => $stmt->affected_rows > 0]);