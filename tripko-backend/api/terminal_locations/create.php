<?php
require_once(__DIR__ . '/../../config/db.php');
header("Content-Type: application/json; charset=UTF-8");

$data = json_decode(file_get_contents('php://input'), true);
$name = $data['name'] ?? '';
$town = $data['town'] ?? '';
$coordinates = $data['coordinates'] ?? '';

if (!$name || !$town || !$coordinates) {
    echo json_encode(['success' => false, 'message' => 'Missing fields']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO route_terminals (name, town, coordinates) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $town, $coordinates);
$stmt->execute();

echo json_encode(['success' => $stmt->affected_rows > 0]);