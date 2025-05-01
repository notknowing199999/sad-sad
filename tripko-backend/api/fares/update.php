<?php
require_once(__DIR__ . '/../../config/db.php');
header("Content-Type: application/json; charset=UTF-8");

$data = json_decode(file_get_contents('php://input'), true);

$stmt = $conn->prepare("UPDATE fares SET from_terminal_id=?, to_terminal_id=?, transport_type_id=?, category=?, amount=? WHERE fare_id=?");
$stmt->bind_param(
    "iiisdi",
    $data['from_terminal_id'],
    $data['to_terminal_id'],
    $data['transport_type_id'],
    $data['category'],
    $data['amount'],
    $data['fare_id']
);
$stmt->execute();

echo json_encode(['success' => true]);