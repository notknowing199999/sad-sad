<?php
class Review {
    private $conn;
    private $table = 'reviews';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($data) {
        try {
            // Validate required fields
            $required_fields = ['user_id', 'spot_id', 'rating', 'content'];
            foreach ($required_fields as $field) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    throw new Exception("Missing required field: {$field}");
                }
            }

            // Additional validation
            if (!is_numeric($data['rating']) || $data['rating'] < 1 || $data['rating'] > 5) {
                throw new Exception("Invalid rating value");
            }

            if (strlen($data['content']) < 10 || strlen($data['content']) > 1000) {
                throw new Exception("Review content must be between 10 and 1000 characters");
            }

            // Prepare query
            $query = "INSERT INTO " . $this->table . "
                    (user_id, spot_id, rating, content, created_at)
                    VALUES (?, ?, ?, ?, NOW())";

            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Failed to prepare query: " . $this->conn->error);
            }

            // Sanitize and bind parameters
            $user_id = htmlspecialchars(strip_tags($data['user_id']));
            $spot_id = htmlspecialchars(strip_tags($data['spot_id']));
            $rating = htmlspecialchars(strip_tags($data['rating']));
            $content = htmlspecialchars(strip_tags($data['content']));

            $stmt->bind_param("iiis", $user_id, $spot_id, $rating, $content);

            if (!$stmt->execute()) {
                throw new Exception("Failed to create review: " . $stmt->error);
            }

            return true;
        } catch (Exception $e) {
            error_log("Error in Review->create(): " . $e->getMessage());
            return false;
        }
    }

    public function read($spot_id) {
        try {
            $query = "SELECT r.*, u.username, u.profile_image 
                     FROM " . $this->table . " r
                     LEFT JOIN users u ON r.user_id = u.user_id
                     WHERE r.spot_id = ?
                     ORDER BY r.created_at DESC";

            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Failed to prepare query: " . $this->conn->error);
            }

            $stmt->bind_param("i", $spot_id);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to fetch reviews: " . $stmt->error);
            }

            return $stmt->get_result();
        } catch (Exception $e) {
            error_log("Error in Review->read(): " . $e->getMessage());
            return false;
        }
    }

    public function update($review_id, $user_id, $data) {
        try {
            // Verify review ownership
            $check_query = "SELECT user_id FROM " . $this->table . " WHERE review_id = ?";
            $check_stmt = $this->conn->prepare($check_query);
            if (!$check_stmt) {
                throw new Exception("Failed to prepare ownership check query");
            }
            
            $check_stmt->bind_param("i", $review_id);
            if (!$check_stmt->execute()) {
                throw new Exception("Failed to verify review ownership");
            }
            
            $result = $check_stmt->get_result();
            $review = $result->fetch_assoc();
            
            if (!$review) {
                throw new Exception("Review not found");
            }
            
            if ($review['user_id'] != $user_id) {
                throw new Exception("You can only edit your own reviews");
            }

            // Validate update data
            if (isset($data['rating'])) {
                if (!is_numeric($data['rating']) || $data['rating'] < 1 || $data['rating'] > 5) {
                    throw new Exception("Invalid rating value");
                }
            }

            if (isset($data['content'])) {
                if (strlen($data['content']) < 10 || strlen($data['content']) > 1000) {
                    throw new Exception("Review content must be between 10 and 1000 characters");
                }
            }

            // Build update query
            $updateFields = [];
            $types = '';
            $values = [];

            if (isset($data['rating'])) {
                $updateFields[] = "rating = ?";
                $types .= "i";
                $values[] = $data['rating'];
            }

            if (isset($data['content'])) {
                $updateFields[] = "content = ?";
                $types .= "s";
                $values[] = htmlspecialchars(strip_tags($data['content']));
            }

            if (empty($updateFields)) {
                throw new Exception("No fields to update");
            }

            $updateFields[] = "updated_at = NOW()";
            
            // Add review_id to values and types
            $types .= "i";
            $values[] = $review_id;

            $query = "UPDATE " . $this->table . " 
                     SET " . implode(", ", $updateFields) . "
                     WHERE review_id = ?";

            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Failed to prepare update query");
            }

            $stmt->bind_param($types, ...$values);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to update review");
            }

            return true;
        } catch (Exception $e) {
            error_log("Error in Review->update(): " . $e->getMessage());
            return false;
        }
    }

    public function delete($review_id, $user_id) {
        try {
            // Verify review ownership
            $check_query = "SELECT user_id, spot_id FROM " . $this->table . " WHERE review_id = ?";
            $check_stmt = $this->conn->prepare($check_query);
            if (!$check_stmt) {
                throw new Exception("Failed to prepare ownership check query");
            }
            
            $check_stmt->bind_param("i", $review_id);
            if (!$check_stmt->execute()) {
                throw new Exception("Failed to verify review ownership");
            }
            
            $result = $check_stmt->get_result();
            $review = $result->fetch_assoc();
            
            if (!$review) {
                throw new Exception("Review not found");
            }
            
            if ($review['user_id'] != $user_id) {
                throw new Exception("You can only delete your own reviews");
            }

            // Delete the review
            $query = "DELETE FROM " . $this->table . " WHERE review_id = ?";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Failed to prepare delete query");
            }

            $stmt->bind_param("i", $review_id);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to delete review");
            }

            // Update spot's average rating
            $update_rating = "UPDATE tourist_spots ts 
                            SET average_rating = (
                                SELECT AVG(rating) 
                                FROM " . $this->table . "
                                WHERE spot_id = ts.spot_id
                            )
                            WHERE spot_id = ?";
            
            $rating_stmt = $this->conn->prepare($update_rating);
            if ($rating_stmt) {
                $rating_stmt->bind_param("i", $review['spot_id']);
                $rating_stmt->execute();
            }

            return true;
        } catch (Exception $e) {
            error_log("Error in Review->delete(): " . $e->getMessage());
            return false;
        }
    }

    public function getUserReviews($user_id) {
        try {
            $query = "SELECT r.*, ts.name as spot_name, ts.status as spot_status
                     FROM " . $this->table . " r
                     LEFT JOIN tourist_spots ts ON r.spot_id = ts.spot_id
                     WHERE r.user_id = ?
                     ORDER BY r.created_at DESC";

            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Failed to prepare query");
            }

            $stmt->bind_param("i", $user_id);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to fetch user reviews");
            }

            return $stmt->get_result();
        } catch (Exception $e) {
            error_log("Error in Review->getUserReviews(): " . $e->getMessage());
            return false;
        }
    }
}