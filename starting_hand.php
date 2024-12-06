<?php
header('Content-Type: application/json');

// Database connection
$host = "localhost";
$username = "mhussain7";
$password = "mhussain7";
$dbname = "mhussain7";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed."]);
    exit();
}

session_start();
if (!isset($_SESSION['username'])) {
    echo json_encode(["success" => false, "message" => "User not logged in."]);
    exit();
}

if (!isset($_GET['deck_id'])) {
    echo json_encode(["success" => false, "message" => "Deck ID is required."]);
    exit();
}

$deck_id = intval($_GET['deck_id']);
$username = $_SESSION['username'];

// Fetch the deck data
$sql = "
    SELECT d.cards
    FROM decks d
    INNER JOIN yugiohusers u ON d.user_id = u.id
    WHERE d.deck_id = ? AND u.username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('is', $deck_id, $username);
$stmt->execute();
$result = $stmt->get_result();

if ($deck = $result->fetch_assoc()) {
    $cards = json_decode($deck['cards'], true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($cards)) {
        echo json_encode(["success" => false, "message" => "Invalid deck data."]);
        exit();
    }

    // Flatten cards into an array based on quantities
    $cardPool = [];
    foreach ($cards as $cardName => $cardDetails) {
        for ($i = 0; $i < $cardDetails['quantity']; $i++) {
            $cardPool[] = $cardName;
        }
    }

    // Ensure at least 5 cards exist
    if (count($cardPool) < 5) {
        echo json_encode(["success" => false, "message" => "Deck must have at least 5 cards to draw a starting hand."]);
        exit();
    }

    // Shuffle and pick the first 5 cards
    shuffle($cardPool);
    $startingHand = array_slice($cardPool, 0, 5);

    echo json_encode(["success" => true, "cards" => $startingHand]);
} else {
    echo json_encode(["success" => false, "message" => "Deck not found."]);
}

$conn->close();
?>
