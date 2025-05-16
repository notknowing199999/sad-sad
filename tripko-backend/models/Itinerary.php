<?php
class Itinerary {
    private $conn;
    private $table_name = "itineraries";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        $query = "SELECT i.*, t.name as town_name 
                 FROM " . $this->table_name . " i
                 LEFT JOIN towns t ON i.destination_id = t.town_id
                 ORDER BY i.name";
                 
        return $this->conn->query($query);
    }
}
