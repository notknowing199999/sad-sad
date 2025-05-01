<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

header("Content-Type: application/json; charset=UTF-8");

require_once(__DIR__ . '/../../config/db.php');

try {
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Not authenticated');
    }

    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        throw new Exception('Method not allowed');
    }

    $period = isset($_GET['period']) ? intval($_GET['period']) : 30;
    $date = date('Y-m-d', strtotime("-$period days"));
    
    error_log("Reports API called - Period: $period, Date range: $date to " . date('Y-m-d'));

    // Tourism Statistics 
    $tourismSql = "
        SELECT 
            ts.name AS spot_name,
            t.town_name,
            COALESCE(SUM(vt.visitor_count), 0) AS visit_count
        FROM tourist_spots ts
        LEFT JOIN towns t ON ts.town_id = t.town_id
        LEFT JOIN visitors_tracking vt ON ts.spot_id = vt.spot_id 
            AND vt.visit_date >= ?
        GROUP BY ts.spot_id, ts.name, t.town_name
        ORDER BY visit_count DESC
        LIMIT 1";
    
    $stmt = $conn->prepare($tourismSql);
    if (!$stmt) {
        throw new Exception("Tourism query preparation failed: " . $conn->error);
    }
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $tourismResult = $stmt->get_result()->fetch_assoc();
    error_log("Tourism result: " . json_encode($tourismResult));

    // Total visitors with trend
    $totalVisitorsSql = "
        SELECT 
            COALESCE(SUM(CASE WHEN visit_date >= ? THEN visitor_count ELSE 0 END), 0) as total,
            COALESCE(SUM(CASE WHEN visit_date >= DATE_SUB(?, INTERVAL $period DAY) 
                              AND visit_date < ? THEN visitor_count ELSE 0 END), 0) as previous_total
        FROM visitors_tracking";
    $stmt = $conn->prepare($totalVisitorsSql);
    $stmt->bind_param("sss", $date, $date, $date);
    $stmt->execute();
    $visitorResult = $stmt->get_result()->fetch_assoc();
    error_log("Visitor counts: " . json_encode($visitorResult));

    // Monthly visitor data using a stable approach for all MySQL versions
    $monthlyVisitorsSql = "
        SELECT 
            DATE_FORMAT(visit_date, '%Y-%m') as month,
            COALESCE(SUM(visitor_count), 0) as count
        FROM visitors_tracking
        WHERE visit_date >= DATE_SUB(CURRENT_DATE, INTERVAL 12 MONTH)
        GROUP BY month 
        ORDER BY month ASC";
    $stmt = $conn->prepare($monthlyVisitorsSql);
    $stmt->execute();
    $monthlyVisitors = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    error_log("Monthly visitor data count: " . count($monthlyVisitors));

    // Transportation Analytics
    $routeSql = "
        SELECT 
            CONCAT(rt1.name, ' → ', rt2.name) as route_name,
            rt1.town as from_town,
            rt2.town as to_town,
            COUNT(DISTINCT tr.route_id) as route_count
        FROM transport_route tr
        JOIN route_terminals rt1 ON tr.origin_terminal_id = rt1.terminal_id
        JOIN route_terminals rt2 ON tr.destination_terminal_id = rt2.terminal_id
        GROUP BY route_name, from_town, to_town
        ORDER BY route_count DESC
        LIMIT 1";
    $stmt = $conn->prepare($routeSql);
    $stmt->execute();
    $routeResult = $stmt->get_result()->fetch_assoc();
    error_log("Route result: " . json_encode($routeResult));

    // Transport types distribution
    $transportTypesSql = "
        SELECT 
            tt.type,
            COALESCE(COUNT(DISTINCT rtt.route_id), 0) as count
        FROM transportation_type tt
        LEFT JOIN route_transport_types rtt ON tt.transport_type_id = rtt.transport_type_id
        GROUP BY tt.transport_type_id, tt.type
        ORDER BY count DESC, type ASC";
    
    error_log("Running transport types query: " . $transportTypesSql);
    $stmt = $conn->prepare($transportTypesSql);
    $stmt->execute();
    $transportTypes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    error_log("Transport types result: " . json_encode($transportTypes));

    // Fill in missing months in visitor data
    $allMonths = [];
    $startDate = new DateTime(date('Y-m-01', strtotime('-11 months')));
    $endDate = new DateTime(date('Y-m-01'));
    
    while ($startDate <= $endDate) {
        $month = $startDate->format('Y-m');
        $allMonths[$month] = 0;
        $startDate->modify('+1 month');
    }

    foreach ($monthlyVisitors as $record) {
        $allMonths[$record['month']] = (int)$record['count'];
    }

    $monthlyData = [];
    foreach ($allMonths as $month => $count) {
        $monthlyData[] = ['month' => $month, 'count' => $count];
    }

    // Calculate trend percentages
    $visitorTrend = $visitorResult['previous_total'] > 0 
        ? (($visitorResult['total'] - $visitorResult['previous_total']) / $visitorResult['previous_total'] * 100)
        : 0;

    // Ensure we always have a success response even with no data
    $response = [
        'success' => true,
        'tourism' => [
            'popularSpot' => $tourismResult['spot_name'] ?? 'No data available',
            'popularSpotLocation' => $tourismResult['town_name'] ?? '',
            'totalVisitors' => (int)($visitorResult['total'] ?? 0),
            'visitorTrend' => round($visitorTrend, 1),
            'monthlyData' => $monthlyData
        ],
        'transport' => [
            'popularRoute' => [
                'name' => $routeResult['route_name'] ?? 'No routes available',
                'fromTown' => $routeResult['from_town'] ?? '',
                'toTown' => $routeResult['to_town'] ?? ''
            ],
            'typeDistribution' => array_map(function($type) {
                return [
                    'type' => $type['type'],
                    'count' => (int)$type['count']
                ];
            }, $transportTypes)
        ]
    ];

    error_log("Final response: " . json_encode($response));
    echo json_encode($response);

} catch (Exception $e) {
    error_log("Reports API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error generating reports: ' . $e->getMessage()
    ]);
}
?>