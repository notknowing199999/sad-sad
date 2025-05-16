<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = "localhost";
$username = "root";
$password = "";
$database = "tripko_db";

try {
    $conn = new mysqli($host, $username, $password, $database);
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Read and execute the SQL file
    $sql = file_get_contents('update_all_town_images.sql');
    if (!$sql) {
        throw new Exception("Failed to read SQL file");
    }

    // Execute each statement
    $statements = explode(';', $sql);
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            if (!$conn->query($statement)) {
                throw new Exception("Failed to execute statement: " . $conn->error);
            }
            echo "Executed statement successfully\n";
        }
    }

    // Additional images need to be downloaded for other municipalities
    // This part will be handled by another script or manual process

    echo "Successfully updated town images\n";
    $conn->close();

} catch (Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}
?>
