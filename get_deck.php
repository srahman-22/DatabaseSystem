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
    echo json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]);
    ob_end_clean(); // Clear any extra output
    exit();
}

session_start();
if (!isset($_SESSION['username'])) {
    echo json_encode(["success" => false, "message" => "User not logged in."]);
    ob_end_clean(); // Clear any extra output
    exit();
}

if (!isset($_GET['deck_id'])) {
    echo json_encode(["success" => false, "message" => "Deck ID is required."]);
    ob_end_clean(); // Clear any extra output
    exit();
}

$deck_id = intval($_GET['deck_id']);
$username = $_SESSION['username'];

// Query to fetch the user's deck
$sql = "
    SELECT d.cards
    FROM decks d
    INNER JOIN yugiohusers u ON d.user_id = u.id
    WHERE d.deck_id = ? AND u.username = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Failed to prepare SQL: " . $conn->error]);
    ob_end_clean(); // Clear any extra output
    exit();
}
$stmt->bind_param('is', $deck_id, $username);
$stmt->execute();
$result = $stmt->get_result();

if ($deck = $result->fetch_assoc()) {
    $decoded_cards = json_decode($deck['cards'], true);
    if (json_last_error() === JSON_ERROR_NONE) {
        ob_end_clean(); // Clear any extra output
        echo json_encode(["success" => true, "cards" => $decoded_cards]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to decode cards JSON."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "No deck found with the specified ID."]);
}

$conn->close();
ob_end_flush(); // Ensure only JSON output
?>
