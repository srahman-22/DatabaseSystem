<?php
header('Content-Type: application/json');
ob_start(); // Start output buffering

// Database connection
$host = "localhost";
$username = "mhussain7";
$password = "mhussain7";
$dbname = "mhussain7";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    ob_end_clean(); // Clear buffer on error
    echo json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]);
    exit();
}

session_start();
if (!isset($_SESSION['username'])) {
    ob_end_clean(); // Clear buffer on error
    echo json_encode(["success" => false, "message" => "User not logged in."]);
    exit();
}

$table = "decks";

$username = $_SESSION['username'];
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !isset($data['cards'])) {
    ob_end_clean(); // Clear buffer on error
    echo json_encode(["success" => false, "message" => "Invalid input."]);
    exit();
}

$cardsJson = json_encode($data['cards']);
$deckId = isset($data['deck_id']) ? intval($data['deck_id']) : null;

$userQuery = "SELECT id FROM yugiohusers WHERE username = ?";
$userStmt = $conn->prepare($userQuery);
$userStmt->bind_param('s', $username);
$userStmt->execute();
$userResult = $userStmt->get_result();

if ($userResult->num_rows === 0) {
    ob_end_clean(); // Clear buffer on error
    echo json_encode(["success" => false, "message" => "User not found."]);
    exit();
}

$userRow = $userResult->fetch_assoc();
$user_id = $userRow['id'];

if ($deckId) {
    $sql = "UPDATE $table SET cards = ? WHERE user_id = ? AND deck_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sii', $cardsJson, $user_id, $deckId);
} else {
    $sql = "INSERT INTO $table (user_id, cards) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('is', $user_id, $cardsJson);
}

if ($stmt->execute()) {
    ob_end_clean(); // Clear buffer on success
    $message = $deckId ? "Deck updated successfully." : "Deck saved successfully.";
    echo json_encode(["success" => true, "message" => $message]);
} else {
    ob_end_clean(); // Clear buffer on error
    echo json_encode(["success" => false, "message" => "Failed to save deck: " . $stmt->error]);
}

$conn->close();
?>
