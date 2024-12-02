<?php
require 'db_connection.php'; // Include your database connection

// Simulate user_id if not logged in (replace with a temporary session or unique identifier for non-logged-in users)
session_start();
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 'guest_' . session_id(); // Create a unique guest user_id
}
$user_id = $_SESSION['user_id']; 

$action = $_GET['action'] ?? '';
$card_id = intval($_GET['card_id'] ?? 0);
<?php
require 'db_connection.php'; // Include your database connection

// Simulate user_id if not logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 'guest_' . session_id(); // Create a unique guest user_id
}
$user_id = $_SESSION['user_id'];

$action = $_GET['action'] ?? '';
$card_id = intval($_GET['card_id'] ?? 0);

if (!$card_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid card ID']);
    exit();
}

// Fetch the current card quantity in the user's deck
$query = $db->prepare("SELECT quantity FROM decks WHERE user_id = ? AND card_id = ?");
$query->execute([$user_id, $card_id]);
$current = $query->fetchColumn();

if ($action === 'add') {
    if ($current >= 3) {
        echo json_encode(['success' => false, 'message' => 'Cannot add more than 3 copies of a card.']);
    } elseif ($current) {
        // Increase quantity if the card is already in the deck
        $query = $db->prepare("UPDATE decks SET quantity = quantity + 1 WHERE user_id = ? AND card_id = ?");
        $query->execute([$user_id, $card_id]);
        echo json_encode(['success' => true, 'message' => 'Card quantity updated.']);
    } else {
        // Add the card to the deck if it's not already present
        $query = $db->prepare("INSERT INTO decks (user_id, card_id, quantity) VALUES (?, ?, 1)");
        $query->execute([$user_id, $card_id]);
        echo json_encode(['success' => true, 'message' => 'Card added to your deck.']);
    }
} elseif ($action === 'remove') {
    if ($current > 1) {
        // Decrease quantity if more than 1 copy exists
        $query = $db->prepare("UPDATE decks SET quantity = quantity - 1 WHERE user_id = ? AND card_id = ?");
        $query->execute([$user_id, $card_id]);
        echo json_encode(['success' => true, 'message' => 'Card quantity reduced.']);
    } elseif ($current) {
        // Remove the card if only 1 copy exists
        $query = $db->prepare("DELETE FROM decks WHERE user_id = ? AND card_id = ?");
        $query->execute([$user_id, $card_id]);
        echo json_encode(['success' => true, 'message' => 'Card removed from your deck.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'This card is not in your deck.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action.']);
}
