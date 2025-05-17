<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/db.php';

try {
    // Get posted data
    $itinerary_id = isset($_POST['itinerary_id']) ? $_POST['itinerary_id'] : die(json_encode(["success" => false, "message" => "Missing itinerary ID"]));
    $town_id = isset($_POST['destination_id']) ? $_POST['destination_id'] : null;
    $name = isset($_POST['name']) ? $_POST['name'] : null;
    $description = isset($_POST['description']) ? $_POST['description'] : null;
    $environmental_fee = isset($_POST['environmental_fee']) ? $_POST['environmental_fee'] : null;

    // Validate required fields
    if (!$name || !$description || !$town_id) {
        die(json_encode([
            "success" => false,
            "message" => "Missing required fields"
        ]));
    }

    // Start transaction
    $conn->begin_transaction();

    // Update itinerary basic info
    $stmt = $conn->prepare("UPDATE itineraries SET 
        town_id = ?, 
        name = ?, 
        description = ?, 
        environmental_fee = ?
        WHERE itinerary_id = ?");

    $stmt->bind_param("isssi", 
        $town_id,
        $name,
        $description,
        $environmental_fee,
        $itinerary_id
    );

    if (!$stmt->execute()) {
        throw new Exception("Failed to update itinerary");
    }

    // Handle image uploads if any
    if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
        $uploadDir = "../../../uploads/";
        $uploadedFiles = [];
        
        // Create uploads directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Process each uploaded file
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            $file_name = $_FILES['images']['name'][$key];
            $file_size = $_FILES['images']['size'][$key];
            $file_tmp = $_FILES['images']['tmp_name'][$key];
            
            // Generate unique filename
            $uniqueName = uniqid() . '_' . $file_name;
            $targetFile = $uploadDir . $uniqueName;
            
            // Move uploaded file
            if (move_uploaded_file($file_tmp, $targetFile)) {
                $uploadedFiles[] = $uniqueName;
                
                // Update image path in database
                $stmt = $conn->prepare("UPDATE itineraries SET image_path = ? WHERE itinerary_id = ?");
                $stmt->bind_param("si", $uniqueName, $itinerary_id);
                
                if (!$stmt->execute()) {
                    throw new Exception("Failed to update image path");
                }
            } else {
                throw new Exception("Failed to upload image: " . $file_name);
            }
        }
    }

    // Commit transaction
    $conn->commit();

    // Return success response
    echo json_encode([
        "success" => true,
        "message" => "Itinerary updated successfully"
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}

$conn->close();