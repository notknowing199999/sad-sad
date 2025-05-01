<?php
require_once(__DIR__ . '/../../config/db.php');
header("Content-Type: application/json; charset=UTF-8");

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['route_id'] ?? '';
$origin_terminal_id = $data['origin_terminal_id'] ?? '';
$destination_terminal_id = $data['destination_terminal_id'] ?? '';
$transport_type_ids = $data['transport_type_ids'] ?? [];

if (!$id || !$origin_terminal_id || !$destination_terminal_id || empty($transport_type_ids)) {
    echo json_encode(['success' => false, 'message' => 'Missing fields']);
    exit;
}

$conn->begin_transaction();

try {
    // Update the main route
    $stmt = $conn->prepare("UPDATE transport_route SET origin_terminal_id=?, destination_terminal_id=? WHERE route_id=?");
    $stmt->bind_param("iii", $origin_terminal_id, $destination_terminal_id, $id);
    $stmt->execute();

    // Remove all existing transport type associations
    $stmt = $conn->prepare("DELETE FROM route_transport_types WHERE route_id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // Insert new transport type associations
    $stmt = $conn->prepare("INSERT INTO route_transport_types (route_id, transport_type_id) VALUES (?, ?)");
    foreach ($transport_type_ids as $type_id) {
        $stmt->bind_param("ii", $id, $type_id);
        $stmt->execute();
    }

    $conn->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}