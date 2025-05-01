<?php
require_once(__DIR__ . '/../../config/db.php');
header("Content-Type: application/json; charset=UTF-8");

$data = json_decode(file_get_contents('php://input'), true);

$stmt = $conn->prepare("DELETE FROM fares WHERE fare_id=?");
$stmt->bind_param("i", $data['fare_id']);
$stmt->execute();

echo json_encode(['success' => true]);