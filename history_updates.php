<?php
// history_updates.php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('Access-Control-Allow-Origin: *');

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'Final_DR');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the last ID we've seen (from the client)
$lastId = isset($_GET['last_id']) ? intval($_GET['last_id']) : 0;

// Set script to run indefinitely
set_time_limit(0);

// Disable output buffering
while (ob_get_level()) ob_end_clean();

// Function to send SSE message
function sendSSE($id, $data)
{
    echo "id: $id\n";
    echo "data: " . json_encode($data) . "\n\n";
    flush();
}

// Keep connection alive
$heartbeat = 0;
while (true) {
    // Check for new records
    $query = "SELECT * FROM history WHERE id > ? ORDER BY id ASC LIMIT 10";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $lastId);
    $stmt->execute();
    $result = $stmt->get_result();

    $newRecords = [];
    while ($row = $result->fetch_assoc()) {
        $newRecords[] = $row;
        $lastId = max($lastId, $row['id']);
    }

    // If we have new records, send them
    if (!empty($newRecords)) {
        sendSSE($lastId, [
            'type' => 'new_records',
            'data' => $newRecords,
            'count' => count($newRecords),
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    } else {
        // Send heartbeat every 10 seconds to keep connection alive
        if ($heartbeat % 10 == 0) {
            sendSSE($lastId, [
                'type' => 'heartbeat',
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        }
        $heartbeat++;
    }

    // Wait for 2 seconds before checking again
    sleep(2);
}

$conn->close();
