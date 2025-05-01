<?php
require_once(__DIR__ . '/../../config/db.php');
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid method']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$transportation = $data['transportation'] ?? '';
$type = $data['type'] ?? '';

if (!$transportation || !$type) {
    echo json_encode(['success' => false, 'message' => 'Missing fields']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO transportation_type (transportation, type) VALUES (?, ?)");
$stmt->bind_param("ss", $transportation, $type);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Insert failed']);
}