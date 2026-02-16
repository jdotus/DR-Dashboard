<?php
// config.php
session_start();

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'Final_DR');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['error' => "Connection failed: " . $conn->connect_error]));
}

// Set charset to UTF-8
$conn->set_charset("utf8mb4");

// Enable error reporting for MySQLi
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Helper function to clean numeric input
function cleanNumeric($value)
{
    return floatval(str_replace(',', '', $value ?? '0'));
}

// Helper function to fetch related records
function getRelatedRecords($conn, $table, $dr_number)
{
    $stmt = $conn->prepare("SELECT * FROM $table WHERE dr_number = ?");
    $stmt->bind_param("i", $dr_number);
    $stmt->execute();
    $result = $stmt->get_result();
    $records = [];
    while ($row = $result->fetch_assoc()) {
        $records[] = $row;
    }
    $stmt->close();
    return $records;
}
