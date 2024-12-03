<?php
header('Content-Type: application/json'); // Set response content type
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start(); // Start output buffering

// Database connection
$host = "localhost";
$username = "mhussain7";
$password = "mhussain7";
$dbname = "mhussain7";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    // Output error message to the console
    $errorMessage = "Database connection failed: " . $conn->connect_error;
    error_log($errorMessage); // Send error to the browser console
    echo json_encode(["success" => false, "message" => $errorMessage]);
    exit();
}

session_start();
if (!isset($_SESSION['user_id']) && !isset($_SESSION['username'])) {
    $errorMessage = "Please log in to save your deck.";
    error_log($errorMessage); // Send error to the browser console
    echo json_encode(["success" => false, "message" => $errorMessage]);
    exit();
}

// Determine user_id
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : $_SESSION['username'];

// Decode the JSON payload
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !isset($data['cards']) || !is_array($data['cards'])) {
    $errorMessage = "Invalid data format received.";
    error_log("Invalid Input: " . print_r($data, true)); // Send error to the console
    echo json_encode(["success" => false, "message" => $errorMessage]);
    exit();
}

// Prepare the cards JSON array
$cards = [];
foreach ($data['cards'] as $card) {
    if (!isset($card['card_id']) || !isset($card['quantity'])) {
        $errorMessage = "Invalid card data.";
        error_log("Card Error: " . print_r($card, true)); // Send error to the console
        echo json_encode(["success" => false, "message" => $errorMessage]);
        exit();
    }

    $card_id = intval($card['card_id']);
    $quantity = intval($card['quantity']);

    // Validate card ID exists in the database
    $stmt = $conn->prepare("SELECT id FROM card WHERE id = ?");
    $stmt->bind_param('i', $card_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        $errorMessage = "Invalid card ID: $card_id";
        error_log($errorMessage); // Send error to the console
        echo json_encode(["success" => false, "message" => $errorMessage]);
        exit();
    }

    // Add card to the array
    $cards[] = ["card_id" => $card_id, "quantity" => $quantity];
}

// Convert the cards array to JSON
$cardsJson = json_encode($cards);

// Check if the user already has a deck
$stmt = $conn->prepare("SELECT deck_id FROM decks WHERE user_id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Update the existing deck
    $stmt = $conn->prepare("UPDATE decks SET cards = ? WHERE user_id = ?");
    $stmt->bind_param('si', $cardsJson, $user_id);
    $stmt->execute();
    error_log("Deck Updated: " . print_r(["user_id" => $user_id, "cards" => $cardsJson], true)); // Console log
} else {
    // Insert a new deck
    $stmt = $conn->prepare("INSERT INTO decks (user_id, cards) VALUES (?, ?)");
    $stmt->bind_param('is', $user_id, $cardsJson);
    $stmt->execute();
    error_log("New Deck Inserted: " . print_r(["user_id" => $user_id, "cards" => $cardsJson], true)); // Console log
}

// Capture unexpected output
$unexpectedOutput = ob_get_contents();
if (!empty($unexpectedOutput)) {
    error_log("Unexpected Output:\n" . $unexpectedOutput); // Log unexpected output
}

ob_end_clean(); // Clear buffer to ensure no extra output

// Send the JSON response
$response = ["success" => true, "message" => "Deck saved successfully"];
error_log("Final Response Sent: " . json_encode($response)); // Log the response to the console
echo json_encode($response);

$conn->close();
?>
