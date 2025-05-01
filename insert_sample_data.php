<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . '/tripko-backend/config/db.php');

try {
    // Start transaction
    $conn->begin_transaction();

    // Clear existing sample data to avoid conflicts
    $conn->query("DELETE FROM visitors_tracking");
    $conn->query("DELETE FROM route_transport_types");
    $conn->query("DELETE FROM transport_route WHERE route_id > 1");
    $conn->query("DELETE FROM festivals");

    // Add sample tourist spots (if they don't exist)
    $spotsSql = "INSERT IGNORE INTO tourist_spots (name, description, category, town_id, contact_info, status) VALUES
        ('Hundred Islands', 'Famous tourist destination featuring 124 islands at low tide', 'Islands', 3, '+63 923 456 7890', 'active'),
        ('Bolinao Lighthouse', 'Historic lighthouse with panoramic views', 'Historical', 14, '+63 934 567 8901', 'active'),
        ('Patar White Beach', 'Beautiful white sand beach', 'Beach', 14, '+63 945 678 9012', 'active')";
    $conn->query($spotsSql);

    // Add sample visitor data for the past 90 days
    $startDate = date('Y-m-d', strtotime('-90 days'));
    $endDate = date('Y-m-d');
    $spotIds = [1]; // Start with existing spot ID

    // Get actual spot IDs from database
    $spotResult = $conn->query("SELECT spot_id FROM tourist_spots");
    $spotIds = [];
    while ($row = $spotResult->fetch_assoc()) {
        $spotIds[] = $row['spot_id'];
    }

    if (!empty($spotIds)) {
        $visitorSql = "INSERT INTO visitors_tracking (spot_id, visit_date, visitor_count) VALUES ";
        $values = [];
        
        $currentDate = strtotime($startDate);
        $endTimestamp = strtotime($endDate);
        
        while ($currentDate <= $endTimestamp) {
            foreach ($spotIds as $spotId) {
                $isWeekend = date('N', $currentDate) >= 6;
                $baseVisitors = $isWeekend ? rand(50, 200) : rand(20, 100);
                
                $month = date('n', $currentDate);
                if ($month >= 3 && $month <= 5) {
                    $baseVisitors *= 1.5;
                }
                
                $values[] = sprintf("(%d, '%s', %d)",
                    $spotId,
                    date('Y-m-d', $currentDate),
                    round($baseVisitors)
                );
            }
            $currentDate = strtotime('+1 day', $currentDate);
        }
        
        if (!empty($values)) {
            $visitorSql .= implode(',', $values);
            $conn->query($visitorSql);
        }
    }

    // Add sample festivals
    $festivalsSql = "INSERT INTO festivals (name, description, date, town_id, image_path) VALUES
        ('Binungey Festival', 'Celebration of the local delicacy binungey', '2025-05-15', 14, 'binungey-fest.jpg'),
        ('Hundred Islands Festival', 'Annual celebration of Alaminos culture', '2025-04-30', 3, 'alaminos.jpg'),
        ('Pista\'y Dayat', 'Sea Festival celebrating marine resources', '2025-05-01', 22, NULL)";
    $conn->query($festivalsSql);

    // Add sample routes (avoiding duplicate keys)
    $routeSql = "INSERT INTO transport_route (origin_terminal_id, destination_terminal_id) VALUES
        (1, 3), -- Bolinao to Dagupan
        (2, 4), -- Alaminos to Alaminos
        (3, 5)  -- Dagupan to Dagupan";
    $conn->query($routeSql);

    // Get the newly inserted route IDs
    $routeResult = $conn->query("SELECT route_id FROM transport_route ORDER BY route_id");
    $routeIds = [];
    while ($row = $routeResult->fetch_assoc()) {
        $routeIds[] = $row['route_id'];
    }

    // Add route transport types for each route
    foreach ($routeIds as $routeId) {
        $typeIds = [rand(1, 2), rand(3, 4)]; // Randomly assign 2 transport types
        foreach ($typeIds as $typeId) {
            $conn->query("INSERT IGNORE INTO route_transport_types (route_id, transport_type_id) 
                         VALUES ($routeId, $typeId)");
        }
    }

    // Commit transaction
    $conn->commit();
    echo "Sample data inserted successfully!";

} catch (Exception $e) {
    $conn->rollback();
    echo "Error inserting sample data: " . $e->getMessage();
}
?>