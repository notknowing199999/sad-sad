<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once('../../config/Database.php');
require_once('../../config/check_session.php');

header('Content-Type: application/json');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

// Check session and authorization
checkTourismOfficerSession();

// Initialize database connection
$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$town_id = $_SESSION['town_id'] ?? null;

if (!$town_id) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No town assigned to this tourism officer']);
    exit;
}

// GET request - List tourist spots for the town
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        // Check if requesting a specific spot
        if (isset($_GET['id'])) {
            $spot_id = $_GET['id'];
            $query = "SELECT ts.*, t.name as town_name, COALESCE(ts.status, 'active') as status
                     FROM tourist_spots ts
                     INNER JOIN towns t ON ts.town_id = t.town_id
                     WHERE ts.spot_id = ? AND ts.town_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ii", $spot_id, $town_id);
        } else {
            // Get all spots for the town, including status
            $query = "SELECT ts.*, t.name as town_name,
                     COALESCE(ts.status, 'active') as status
                     FROM tourist_spots ts
                     INNER JOIN towns t ON ts.town_id = t.town_id
                     WHERE ts.town_id = ?
                     ORDER BY ts.name ASC";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $town_id);
        }

        if (!$stmt->execute()) {
            throw new Exception('Failed to fetch tourist spots: ' . $conn->error);
        }

        $result = $stmt->get_result();
        
        if (isset($_GET['id'])) {
            // Return single spot
            $spot = $result->fetch_assoc();
            if ($spot) {
                echo json_encode(['success' => true, 'spot' => $spot]);
            } else {
                http_response_code(404);
                echo json_encode([
                    'success' => false, 
                    'message' => 'Tourist spot not found or you do not have permission to access it'
                ]);
            }
        } else {
            // Return all spots
            $spots = [];
            while ($row = $result->fetch_assoc()) {
                $spots[] = $row;
            }
            echo json_encode([
                'success' => true, 
                'spots' => $spots,
                'message' => empty($spots) ? 'No tourist spots found for this municipality' : null
            ]);
        }
    } catch (Exception $e) {
        error_log("Error in tourism officers tourist spots API: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false, 
            'message' => 'An error occurred while fetching tourist spots. Please try again later.'
        ]);
    }
}
// POST request - Create new tourist spot
else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate required fields
        $required = ['name', 'description', 'category', 'location'];
        foreach ($required as $field) {
            if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
                throw new Exception("Missing required field: {$field}");
            }
        }

        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $category = trim($_POST['category']);
        $contact_info = isset($_POST['contact_info']) ? trim($_POST['contact_info']) : null;
        $location = trim($_POST['location']);
        $status = 'active';

        // Handle image upload
        $image_path = null;
        if (isset($_FILES['images']) && $_FILES['images']['error'][0] === UPLOAD_ERR_OK) {
            $uploadDir = "../../../uploads/";
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $filename = uniqid() . '_' . basename($_FILES['images']['name'][0]);
            $targetFile = $uploadDir . $filename;
            
            if (move_uploaded_file($_FILES['images']['tmp_name'][0], $targetFile)) {
                $image_path = $filename;
            }
        }

        // Insert new tourist spot with location
        $query = "INSERT INTO tourist_spots (name, description, category, contact_info, location, image_path, town_id, status) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssssis", $name, $description, $category, $contact_info, $location, $image_path, $town_id, $status);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Tourist spot created successfully']);
        } else {
            throw new Exception($stmt->error);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
// PUT request - Update tourist spot
else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    try {
        parse_str(file_get_contents("php://input"), $_PUT);
        
        if (!isset($_GET['id'])) {
            throw new Exception('Missing spot ID');
        }
        
        $spot_id = $_GET['id'];
        
        // Verify spot belongs to tourism officer's town
        $check = $conn->prepare("SELECT spot_id FROM tourist_spots WHERE spot_id = ? AND town_id = ?");
        $check->bind_param("ii", $spot_id, $town_id);
        $check->execute();
        if ($check->get_result()->num_rows === 0) {
            throw new Exception('Tourist spot not found or unauthorized');
        }

        $updates = [];
        $types = "";
        $values = [];
        
        // Build dynamic update query
        $allowed_fields = ['name', 'description', 'category', 'contact_info', 'location', 'status'];
        foreach ($allowed_fields as $field) {
            if (isset($_PUT[$field])) {
                $updates[] = "{$field} = ?";
                $types .= "s";
                $values[] = $_PUT[$field];
            }
        }

        if (!empty($updates)) {
            $values[] = $spot_id;
            $types .= "i";
            
            $query = "UPDATE tourist_spots SET " . implode(", ", $updates) . " WHERE spot_id = ? AND town_id = ?";
            $values[] = $town_id;
            $types .= "i";
            
            $stmt = $conn->prepare($query);
            $stmt->bind_param($types, ...$values);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Tourist spot updated successfully']);
            } else {
                throw new Exception($stmt->error);
            }
        } else {
            echo json_encode(['success' => true, 'message' => 'No changes to update']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
// DELETE request - Delete tourist spot
else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    try {
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (!isset($data['spot_id'])) {
            throw new Exception('Missing spot ID');
        }
        
        $spot_id = $data['spot_id'];
        
        // Verify spot belongs to tourism officer's town
        $check = $conn->prepare("SELECT image_path FROM tourist_spots WHERE spot_id = ? AND town_id = ?");
        $check->bind_param("ii", $spot_id, $town_id);
        $check->execute();
        $result = $check->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception('Tourist spot not found or unauthorized');
        }

        $spot = $result->fetch_assoc();
        
        // Delete the spot
        $stmt = $conn->prepare("DELETE FROM tourist_spots WHERE spot_id = ? AND town_id = ?");
        $stmt->bind_param("ii", $spot_id, $town_id);
        
        if ($stmt->execute()) {
            // Delete associated image if it exists
            if ($spot['image_path']) {
                $image_path = "../../../uploads/" . $spot['image_path'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
            echo json_encode(['success' => true, 'message' => 'Tourist spot deleted successfully']);
        } else {
            throw new Exception($stmt->error);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>
