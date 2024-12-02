<?php
require 'db_connection.php'; // Include your database connection

// Simulate user_id if not logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 'guest_' . session_id(); // Create a unique guest user_id
}
$user_id = $_SESSION['user_id'];

// Fetch the user's deck
$query = $db->prepare("
    SELECT c.name, d.quantity
    FROM decks d
    JOIN card c ON d.card_id = c.id
    WHERE d.user_id = ?
");
$query->execute([$user_id]);
$deck = $query->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($deck);
