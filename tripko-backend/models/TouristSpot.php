<?php
class TouristSpot {
    private $conn;
    private $table = 'tourist_spots';

    public $spot_id;
    public $name;
    public $description;
    public $town_id;
    public $category;
    public $contact_info;
    public $image_path;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Read all tourist spots (for admin/tourism officers)
    public function read() {
        try {
            $query = "SELECT 
                ts.spot_id, 
                ts.name, 
                ts.description, 
                ts.category,
                ts.town_id,
                t.name as town_name,
                ts.contact_info,
                ts.image_path,
                ts.created_at,
                ts.updated_at,
                COALESCE(ts.status, 'active') as status
            FROM 
                " . $this->table . " ts
            LEFT JOIN
                towns t ON ts.town_id = t.town_id
            ORDER BY
                ts.name ASC";

            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Query preparation failed: " . $this->conn->error);
            }

            if (!$stmt->execute()) {
                throw new Exception("Query execution failed: " . $stmt->error);
            }

            return $stmt->get_result();
        } catch (Exception $e) {
            error_log("Error in TouristSpot->read(): " . $e->getMessage());
            return false;
        }
    }

    // Read only active tourist spots (for regular users)
    public function readActive() {
        try {
            $query = "SELECT 
                ts.spot_id, 
                ts.name, 
                ts.description, 
                ts.category,
                ts.town_id,
                t.name as town_name,
                ts.contact_info,
                ts.image_path,
                ts.created_at,
                ts.updated_at,
                COALESCE(ts.status, 'active') as status
            FROM 
                " . $this->table . " ts
            LEFT JOIN
                towns t ON ts.town_id = t.town_id
            WHERE
                ts.status = 'active' OR ts.status IS NULL
            ORDER BY
                ts.name ASC";

            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Query preparation failed: " . $this->conn->error);
            }

            if (!$stmt->execute()) {
                throw new Exception("Query execution failed: " . $stmt->error);
            }

            return $stmt->get_result();
        } catch (Exception $e) {
            error_log("Error in TouristSpot->readActive(): " . $e->getMessage());
            return false;
        }
    }

    // Search tourist spots
    public function search($keyword) {
        try {
            $query = "SELECT 
                ts.spot_id, 
                ts.name, 
                ts.description, 
                ts.category,
                ts.town_id,
                t.name as town_name,
                ts.contact_info,
                ts.image_path,
                COALESCE(ts.status, 'active') as status
            FROM 
                " . $this->table . " ts
            LEFT JOIN
                towns t ON ts.town_id = t.town_id
            WHERE
                (ts.name LIKE ? OR
                t.name LIKE ? OR
                ts.category LIKE ? OR
                ts.description LIKE ?)
                AND (ts.status = 'active' OR ts.status IS NULL)
            ORDER BY
                ts.name ASC";

            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Query preparation failed: " . $this->conn->error);
            }
            
            $keyword = "%{$keyword}%";
            $stmt->bind_param("ssss", $keyword, $keyword, $keyword, $keyword);
            
            if (!$stmt->execute()) {
                throw new Exception("Query execution failed: " . $stmt->error);
            }
            
            return $stmt->get_result();
        } catch (Exception $e) {
            error_log("Error in TouristSpot->search(): " . $e->getMessage());
            return false;
        }
    }

    // Update tourist spot status
    public function updateStatus($spotId, $status) {
        try {
            // Validate status
            $validStatuses = ['active', 'inactive'];
            if (!in_array($status, $validStatuses)) {
                throw new Exception("Invalid status value");
            }

            $query = "UPDATE " . $this->table . " 
                     SET status = ?, updated_at = NOW()
                     WHERE spot_id = ?";

            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Query preparation failed: " . $this->conn->error);
            }

            $stmt->bind_param("si", $status, $spotId);
            
            if (!$stmt->execute()) {
                throw new Exception("Query execution failed: " . $stmt->error);
            }

            return true;
        } catch (Exception $e) {
            error_log("Error in TouristSpot->updateStatus(): " . $e->getMessage());
            return false;
        }
    }

    // Update tourist spot details
    public function update($data) {
        try {
            // Start building update query
            $updateFields = [];
            $types = '';
            $values = [];

            // Define allowed fields and their validation rules
            $allowedFields = [
                'name' => ['type' => 's', 'required' => false],
                'description' => ['type' => 's', 'required' => false],
                'category' => ['type' => 's', 'required' => false],
                'contact_info' => ['type' => 's', 'required' => false],
                'image_path' => ['type' => 's', 'required' => false],
                'status' => ['type' => 's', 'required' => false],
                'town_id' => ['type' => 'i', 'required' => false]
            ];

            // Validate and build query parts
            foreach ($allowedFields as $field => $rules) {
                if (isset($data[$field]) && $data[$field] !== '') {
                    // Special validation for status
                    if ($field === 'status') {
                        if (!in_array($data[$field], ['active', 'inactive'])) {
                            throw new Exception("Invalid status value");
                        }
                    }
                    
                    // Special validation for town_id
                    if ($field === 'town_id') {
                        // Verify town exists
                        $checkTown = $this->conn->prepare("SELECT town_id FROM towns WHERE town_id = ?");
                        if (!$checkTown) {
                            throw new Exception("Failed to prepare town validation query");
                        }
                        
                        $checkTown->bind_param("i", $data[$field]);
                        if (!$checkTown->execute()) {
                            throw new Exception("Failed to validate town");
                        }
                        
                        if ($checkTown->get_result()->num_rows === 0) {
                            throw new Exception("Invalid town ID");
                        }
                    }

                    $updateFields[] = "{$field} = ?";
                    $types .= $rules['type'];
                    $values[] = $data[$field];
                }
            }

            if (empty($updateFields)) {
                throw new Exception("No fields to update");
            }

            // Add spot_id to values array
            $types .= "i"; // for spot_id
            $values[] = $data['spot_id'];

            // Construct final query
            $query = "UPDATE " . $this->table . " 
                     SET " . implode(", ", $updateFields) . ",
                         updated_at = NOW()
                     WHERE spot_id = ?";

            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Failed to prepare update query: " . $this->conn->error);
            }

            // Bind parameters dynamically
            $stmt->bind_param($types, ...$values);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to execute update: " . $stmt->error);
            }

            // Check if any rows were actually updated
            if ($stmt->affected_rows === 0) {
                // Check if the record exists
                $check = $this->conn->prepare("SELECT spot_id FROM {$this->table} WHERE spot_id = ?");
                $check->bind_param("i", $data['spot_id']);
                $check->execute();
                
                if ($check->get_result()->num_rows === 0) {
                    throw new Exception("Tourist spot not found");
                }
                // If record exists but no rows affected, it means no changes were needed
                return true;
            }

            return true;
        } catch (Exception $e) {
            error_log("Error in TouristSpot->update(): " . $e->getMessage());
            return false;
        }
    }

    // Delete a tourist spot
    public function delete($spot_id) {
        try {
            // Validate spot exists before deletion
            $check_query = "SELECT spot_id FROM {$this->table} WHERE spot_id = ?";
            $check_stmt = $this->conn->prepare($check_query);
            if (!$check_stmt) {
                throw new Exception("Failed to prepare existence check query");
            }
            
            $check_stmt->bind_param("i", $spot_id);
            if (!$check_stmt->execute()) {
                throw new Exception("Failed to check if spot exists");
            }
            
            if ($check_stmt->get_result()->num_rows === 0) {
                throw new Exception("Tourist spot not found");
            }

            // Prepare delete query
            $query = "DELETE FROM " . $this->table . " WHERE spot_id = ?";
            $stmt = $this->conn->prepare($query);
            
            if (!$stmt) {
                throw new Exception("Failed to prepare delete query");
            }

            $stmt->bind_param("i", $spot_id);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to execute delete query");
            }

            if ($stmt->affected_rows === 0) {
                throw new Exception("No rows were deleted");
            }

            return true;
        } catch (Exception $e) {
            error_log("Error in TouristSpot->delete(): " . $e->getMessage());
            return false;
        }
    }
}
?>