<?php
require_once(__DIR__ . '/../../config/db.php');
header("Content-Type: application/json; charset=UTF-8");

$sql = "SELECT terminal_id, name, town, coordinates FROM route_terminals ORDER BY terminal_id DESC";
$result = $conn->query($sql);
$records = [];
while ($row = $result->fetch_assoc()) {
    $records[] = $row;
}
echo json_encode(['records' => $records]);