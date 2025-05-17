<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/db.php';

try {
    // Get ID from query string
    $id = isset($_GET['id']) ? intval($_GET['id']) : die(json_encode([
        "success" => false,
        "message" => "Missing itinerary ID"
    ]));

    // Prepare query
    $query = "SELECT i.*, t.name as destination_name
              FROM itineraries i 
              LEFT JOIN towns t ON i.town_id = t.town_id
              WHERE i.itinerary_id = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $itinerary = $result->fetch_assoc();
            
            echo json_encode([
                "success" => true,
                "itinerary" => $itinerary
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "Itinerary not found"
            ]);
        }
    } else {
        throw new Exception("Failed to execute query");
    }
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}

$conn->close();