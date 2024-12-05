<?php
header('Content-Type: application/json');

// Database connection
$host = "localhost";
$username = "mhussain7";
$password = "mhussain7";
$dbname = "mhussain7";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]);
    exit();
}

session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in."]);
    exit();
}

// Set the table being queried
$table = "decks";

$user_id = $_SESSION['user_id'];
$sql = "SELECT cards FROM $table WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($deck = $result->fetch_assoc()) {
    echo json_encode(["success" => true, "deck" => json_decode($deck['cards'], true)]);
} else {
    echo json_encode(["success" => false, "message" => "No deck found."]);
}

$conn->close();
?>
