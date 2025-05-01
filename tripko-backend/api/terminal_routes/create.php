<?php
require_once(__DIR__ . '/../../config/db.php');
header("Content-Type: application/json; charset=UTF-8");

$data = json_decode(file_get_contents('php://input'), true);

$origin_terminal_id = $data['origin_terminal_id'] ?? '';
$destination_terminal_id = $data['destination_terminal_id'] ?? '';
$transport_type_ids = $data['transport_type_ids'] ?? [];

if (!$origin_terminal_id || !$destination_terminal_id || empty($transport_type_ids)) {
    echo json_encode(['success' => false, 'message' => 'Missing fields']);
    exit;
}

$conn->begin_transaction();

try {
    // Create the route
    $stmt = $conn->prepare("INSERT INTO transport_route (origin_terminal_id, destination_terminal_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $origin_terminal_id, $destination_terminal_id);
    $stmt->execute();
    
    $route_id = $conn->insert_id;

    // Then insert all transport types into route_transport_types
    $stmt = $conn->prepare("INSERT INTO route_transport_types (route_id, transport_type_id) VALUES (?, ?)");
    foreach ($transport_type_ids as $type_id) {
        $stmt->bind_param("ii", $route_id, $type_id);
        $stmt->execute();
    }

    $conn->commit();
    echo json_encode(['success' => true, 'route_id' => $route_id]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}