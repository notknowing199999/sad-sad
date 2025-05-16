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
    }    public function read() {
        $query = "SELECT ts.*, t.name as town_name 
                 FROM " . $this->table . " ts 
                 LEFT JOIN towns t ON ts.town_id = t.town_id 
                 ORDER BY ts.name";

        $result = $this->conn->query($query);
        return $result;
    }    public function search($keyword) {
        $query = "SELECT 
            ts.spot_id, 
            ts.name, 
            ts.description, 
            ts.town_id,
            t.name as town_name, 
            ts.category,
            ts.contact_info,
            ts.image_path,
            ts.status
        FROM 
            " . $this->table . " ts
        LEFT JOIN
            towns t ON ts.town_id = t.town_id
        WHERE
            ts.name LIKE ? OR
            t.town_name LIKE ? OR
            ts.category LIKE ? OR
            ts.description LIKE ?
        ORDER BY
            ts.name ASC";

        $stmt = $this->conn->prepare($query);
        
        $keyword = "%{$keyword}%";
        $stmt->bind_param("ssss", $keyword, $keyword, $keyword, $keyword);
        $stmt->execute();
        
        return $stmt->get_result();
    }    public function read_single() {
        $query = "SELECT 
            ts.spot_id, 
            ts.name, 
            ts.description, 
            t.name as town_name, 
            ts.category,
            ts.contact_info,
            ts.image_path,
            ts.status
        FROM 
            " . $this->table . " ts
        LEFT JOIN
            towns t ON ts.town_id = t.town_id
        WHERE
            ts.spot_id = ?
        LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->spot_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }

    public function updateStatus() {
        $query = "UPDATE " . $this->table . "
                SET status = ?
                WHERE spot_id = ?";

        $stmt = $this->conn->prepare($query);

        // Sanitize input
        $this->spot_id = htmlspecialchars(strip_tags($this->spot_id));
        $this->status = htmlspecialchars(strip_tags($this->status));

        // Bind parameters
        $stmt->bind_param("si", $this->status, $this->spot_id);

        return $stmt->execute();
    }
}
