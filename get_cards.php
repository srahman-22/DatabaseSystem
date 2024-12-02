<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$username = "mhussain7";
$password = "mhussain7";
$dbname = "mhussain7";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed"]);
    exit();
}

$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
$type = isset($_GET['type']) ? $_GET['type'] : '';

// Base query
$query = "SELECT id, name, type, quantity FROM card";

// Add type filter if provided
if (!empty($type)) {
    $query .= " WHERE type = ?";
}
$query .= " LIMIT ? OFFSET ?";

$stmt = $conn->prepare($query);

if (!empty($type)) {
    $stmt->bind_param('sii', $type, $limit, $offset);
} else {
    $stmt->bind_param('ii', $limit, $offset);
}

$stmt->execute();
$result = $stmt->get_result();

$cards = [];
while ($row = $result->fetch_assoc()) {
    $cards[] = $row;
}

echo json_encode($cards);

$stmt->close();
$conn->close();
?>
