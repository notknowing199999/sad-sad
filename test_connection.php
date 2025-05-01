<?php
include_once 'config/Database.php';

$database = new Database();
$db = $database->getConnection();

if($db) {
    echo "<br>Database connection is working!";
    
    // Test query
    try {
        $query = "SELECT COUNT(*) as count FROM tourist_spots";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<br>Number of tourist spots: " . $row['count'];
    } catch(PDOException $e) {
        echo "<br>Query error: " . $e->getMessage();
    }
} else {
    echo "<br>Database connection failed!";
}