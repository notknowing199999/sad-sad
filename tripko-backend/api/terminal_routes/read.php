<?php
require_once(__DIR__ . '/../../config/db.php');
header("Content-Type: application/json; charset=UTF-8");

$sql = "SELECT 
    r.route_id,
    t1.name AS from_terminal,
    t1.town AS from_town,
    t2.name AS to_terminal,
    t2.town AS to_town,
    GROUP_CONCAT(tt.type ORDER BY tt.type) AS transportation_types,
    GROUP_CONCAT(tt.transport_type_id ORDER BY tt.type) AS transport_type_ids
  FROM transport_route r
  LEFT JOIN route_terminals t1 ON r.origin_terminal_id = t1.terminal_id
  LEFT JOIN route_terminals t2 ON r.destination_terminal_id = t2.terminal_id
  LEFT JOIN route_transport_types rtt ON r.route_id = rtt.route_id
  LEFT JOIN transportation_type tt ON rtt.transport_type_id = tt.transport_type_id
  GROUP BY r.route_id
  ORDER BY r.route_id DESC";

$result = $conn->query($sql);
$records = [];
while ($row = $result->fetch_assoc()) {
    $records[] = $row;
}
echo json_encode(['records' => $records]);