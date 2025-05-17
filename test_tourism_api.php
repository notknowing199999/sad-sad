<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Simulate a tourism officer session
$_SESSION['user_id'] = 3; // Example tourism officer ID
$_SESSION['user_type_id'] = 3;
$_SESSION['username'] = 'test_officer';
$_SESSION['town_id'] = 2; // Example town ID (Bolinao)
$_SESSION['initialized'] = true;
$_SESSION['expires'] = time() + (2 * 60 * 60);

// Make request to the API
$url = 'http://localhost/tripko-system/tripko-backend/api/tourism_officers/tourist_spots.php';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIE, session_name() . '=' . session_id());

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status Code: " . $httpCode . "\n\n";
echo "Response:\n";
echo json_encode(json_decode($response), JSON_PRETTY_PRINT);
?>
