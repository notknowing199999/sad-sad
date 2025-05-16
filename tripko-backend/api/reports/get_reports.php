<?php
if (!function_exists('http_response_code')) {
    function http_response_code($code = NULL) {
        if ($code !== NULL) {
            switch ($code) {
                case 100: $text = 'Continue'; break;
                case 101: $text = 'Switching Protocols'; break;
                case 200: $text = 'OK'; break;
                case 201: $text = 'Created'; break;
                case 202: $text = 'Accepted'; break;
                case 203: $text = 'Non-Authoritative Information'; break;
                case 204: $text = 'No Content'; break;
                case 205: $text = 'Reset Content'; break;
                case 206: $text = 'Partial Content'; break;
                case 300: $text = 'Multiple Choices'; break;
                case 301: $text = 'Moved Permanently'; break;
                case 302: $text = 'Moved Temporarily'; break;
                case 303: $text = 'See Other'; break;
                case 304: $text = 'Not Modified'; break;
                case 305: $text = 'Use Proxy'; break;
                case 400: $text = 'Bad Request'; break;
                case 401: $text = 'Unauthorized'; break;
                case 402: $text = 'Payment Required'; break;
                case 403: $text = 'Forbidden'; break;
                case 404: $text = 'Not Found'; break;
                case 405: $text = 'Method Not Allowed'; break;
                case 406: $text = 'Not Acceptable'; break;
                case 407: $text = 'Proxy Authentication Required'; break;
                case 408: $text = 'Request Time-out'; break;
                case 409: $text = 'Conflict'; break;
                case 410: $text = 'Gone'; break;
                case 411: $text = 'Length Required'; break;
                case 412: $text = 'Precondition Failed'; break;
                case 413: $text = 'Request Entity Too Large'; break;
                case 414: $text = 'Request-URI Too Large'; break;
                case 415: $text = 'Unsupported Media Type'; break;
                case 500: $text = 'Internal Server Error'; break;
                case 501: $text = 'Not Implemented'; break;
                case 502: $text = 'Bad Gateway'; break;
                case 503: $text = 'Service Unavailable'; break;
                case 504: $text = 'Gateway Time-out'; break;
                case 505: $text = 'HTTP Version not supported'; break;
                default:  exit('Unknown http status code "' . htmlentities($code) . '"'); break;
            }
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . $code . ' ' . $text);
            $GLOBALS['http_response_code'] = $code;
        } else {
            $code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);
        }
        return $code;
    }
}
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../../config/Database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();

    $period = isset($_GET['period']) ? (int)$_GET['period'] : 30;
    $response = ['success' => true];

    // Tourism data query 
    $tourismQuery = "SELECT 
        DATE_FORMAT(ts.created_at, '%Y-%m') as month,
        COUNT(*) as count 
        FROM tourist_spots ts 
        WHERE ts.created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
        GROUP BY DATE_FORMAT(ts.created_at, '%Y-%m')
        ORDER BY month";

    $stmt = $conn->prepare($tourismQuery);
    $stmt->bind_param("i", $period);
    $stmt->execute();
    $tourismResult = $stmt->get_result();
    
    $monthlyData = [];
    while ($row = $tourismResult->fetch_assoc()) {
        $monthlyData[] = $row;
    }

    // Popular spot query
    $spotQuery = "SELECT ts.name, t.name as town_name, 
                  COALESCE(COUNT(*), 0) as visit_count 
                  FROM tourist_spots ts
                  LEFT JOIN towns t ON ts.town_id = t.town_id
                  GROUP BY ts.spot_id 
                  ORDER BY visit_count DESC 
                  LIMIT 1";
                 
    $spotResult = $conn->query($spotQuery);
    $popularSpot = $spotResult->fetch_assoc();

    // Transport distribution - using correct table name
    $transportQuery = "SELECT 
        tt.type as name,
        COALESCE(COUNT(DISTINCT rt.route_id), 0) as count
        FROM transportation_type tt
        LEFT JOIN route_transport_types rtt ON tt.transport_type_id = rtt.transport_type_id
        LEFT JOIN transport_route rt ON rtt.route_id = rt.route_id
        GROUP BY tt.transport_type_id";

    $transportResult = $conn->query($transportQuery);
    $typeDistribution = [];
    while ($row = $transportResult->fetch_assoc()) {
        $typeDistribution[] = $row;
    }

    // Popular route query
    $routeQuery = "SELECT 
        tr.route_id,
        t1.name as from_town,
        t2.name as to_town,
        COUNT(*) as usage_count
        FROM transport_route tr
        LEFT JOIN route_terminals term1 ON tr.origin_terminal_id = term1.terminal_id
        LEFT JOIN route_terminals term2 ON tr.destination_terminal_id = term2.terminal_id
        LEFT JOIN towns t1 ON term1.town = t1.name
        LEFT JOIN towns t2 ON term2.town = t2.name
        GROUP BY tr.route_id
        ORDER BY usage_count DESC
        LIMIT 1";

    $routeResult = $conn->query($routeQuery);
    $popularRoute = $routeResult->fetch_assoc();

    $response['tourism'] = [
        'monthlyData' => $monthlyData,
        'totalVisitors' => array_sum(array_column($monthlyData, 'count')),
        'visitorTrend' => 0,
        'popularSpot' => $popularSpot['name'] ?? 'No data',
        'popularSpotLocation' => $popularSpot['town_name'] ?? null
    ];

    $response['transport'] = [
        'typeDistribution' => $typeDistribution,
        'popularRoute' => [
            'name' => ($popularRoute ? "{$popularRoute['from_town']} to {$popularRoute['to_town']}" : 'No data'),
            'fromTown' => $popularRoute['from_town'] ?? null,
            'toTown' => $popularRoute['to_town'] ?? null
        ]
    ];

    echo json_encode($response);

} catch(Exception $e) {
    error_log("Dashboard error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// Close the database connection
if (isset($conn)) {
    $conn->close();
}
?>