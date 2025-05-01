<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "tripko_db"; 

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["message" => "Database connection failed"]);
    exit;
}

try {
    $sql = "SELECT town_id, town_name FROM towns ORDER BY town_name ASC";
    $result = $conn->query($sql);

    $towns = [];
    while ($row = $result->fetch_assoc()) {
        $towns[] = [
            "town_id" => $row["town_id"],
            "name" => $row["town_name"]
        ];
    }

    echo json_encode(["records" => $towns]);
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(["message" => $e->getMessage()]);
}
$conn->close();
// No closing PHP tag to avoid accidental whitespace