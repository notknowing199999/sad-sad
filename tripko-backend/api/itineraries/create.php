<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once(__DIR__ . '/../../config/db.php');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        exit;
    }

    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $destination_id = $_POST['destination'] ?? '';
    $environmental_fee = $_POST['environmental_fee'] ?? '';

    // Handle image upload (single image for simplicity)
    $image_path = null;
    if (isset($_FILES['images']) && $_FILES['images']['error'][0] === UPLOAD_ERR_OK) {
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/TripKo-System/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $filename = uniqid() . '_' . basename($_FILES['images']['name'][0]);
        $targetFile = $uploadDir . $filename;
        if (move_uploaded_file($_FILES['images']['tmp_name'][0], $targetFile)) {
            $image_path = $filename;
        }
    }

    $stmt = $conn->prepare("INSERT INTO itineraries (name, description, destination_id, environmental_fee, image_path) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiss", $name, $description, $destination_id, $environmental_fee, $image_path);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Insert failed']);
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}