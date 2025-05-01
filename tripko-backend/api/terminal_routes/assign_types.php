<?php
require_once(__DIR__ . '/../../config/db.php');
header("Content-Type: application/json; charset=UTF-8");

$data = json_decode(file_get_contents('php://input'), true);
$route_id = $data['route_id'] ?? '';
$type_ids = $data['transport_type_ids'] ?? [];

if (!$route_id) {
    echo json_encode(['success' => false, 'message' => 'Missing route_id']);
    exit;
}

// Remove all current assignments for this route
$stmt = $conn->prepare("DELETE FROM route_transport_types WHERE route_id=?");
$stmt->bind_param("i", $route_id);
$stmt->execute();

// Insert new assignments
if (!empty($type_ids)) {
    $stmt = $conn->prepare("INSERT INTO route_transport_types (route_id, transport_type_id) VALUES (?, ?)");
    foreach ($type_ids as $type_id) {
        $stmt->bind_param("ii", $route_id, $type_id);
        $stmt->execute();
    }
}

echo json_encode(['success' => true]);