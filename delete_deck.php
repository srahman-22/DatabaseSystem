<?php
header('Content-Type: application/json');

session_start();

if (!isset($_SESSION['username'])) {
    echo json_encode(["success" => false, "message" => "User not logged in."]);
    exit();
}

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

// Check if input is sent via JSON or form data
$input = file_get_contents('php://input');
$data = json_decode($input, true);
if (!$data || !isset($data['deck_id'])) {
    // Fallback to check POST form data
    if (isset($_POST['deck_id'])) {
        $deck_id = intval($_POST['deck_id']);
    } else {
        echo json_encode(["success" => false, "message" => "Invalid input."]);
        exit();
    }
} else {
    $deck_id = intval($data['deck_id']);
}

$username = $_SESSION['username'];

// Verify deck ownership
$sql = "SELECT d.deck_id FROM decks d
        INNER JOIN yugiohusers u ON d.user_id = u.id
        WHERE d.deck_id = ? AND u.username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('is', $deck_id, $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Deck not found or you do not have permission to delete it."]);
    exit();
}

// Delete the deck
$delete_sql = "DELETE FROM decks WHERE deck_id = ?";
$delete_stmt = $conn->prepare($delete_sql);
$delete_stmt->bind_param('i', $deck_id);

if ($delete_stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Deck deleted successfully."]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to delete the deck."]);
}

$conn->close();
?>
