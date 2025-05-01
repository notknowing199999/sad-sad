<?php
require_once(__DIR__ . '/../../config/db.php');
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    $required = ['from_terminal_id', 'to_terminal_id', 'transport_type_id', 'category', 'amount'];
    foreach ($required as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }

    $stmt = $conn->prepare("INSERT INTO fares (from_terminal_id, to_terminal_id, transport_type_id, category, amount) VALUES (?, ?, ?, ?, ?)");
    
    if (!$stmt) {
        throw new Exception($conn->error);
    }

    $stmt->bind_param(
        "iiiss",
        $data['from_terminal_id'],
        $data['to_terminal_id'],
        $data['transport_type_id'],
        $data['category'],
        $data['amount']
    );

    if (!$stmt->execute()) {
        throw new Exception($stmt->error);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Fare created successfully',
        'fare_id' => $conn->insert_id
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$stmt->close();
$conn->close();