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
if (!isset($_SESSION['username'])) {
    echo json_encode(["success" => false, "message" => "User not logged in."]);
    exit();
}

// Set the table being used
$table = "decks";

$username = $_SESSION['username'];
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !isset($data['cards'])) {
    echo json_encode(["success" => false, "message" => "Invalid input."]);
    exit();
}

$cardsJson = json_encode($data['cards']);

// Get the user ID based on the username
$userQuery = "SELECT id FROM yugiohusers WHERE username = ?";
$userStmt = $conn->prepare($userQuery);
$userStmt->bind_param('s', $username);
$userStmt->execute();
$userResult = $userStmt->get_result();

if ($userResult->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "User not found."]);
    exit();
}

$userRow = $userResult->fetch_assoc();
$user_id = $userRow['id'];

$sql = "INSERT INTO $table (user_id, cards) VALUES (?, ?) 
        ON DUPLICATE KEY UPDATE cards = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('iss', $user_id, $cardsJson, $cardsJson);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Deck saved successfully."]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to save deck."]);
}

$conn->close();
?>
