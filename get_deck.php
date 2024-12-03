<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$host = "localhost";
$username = "mhussain7";
$password = "mhussain7";
$dbname = "mhussain7";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed"]);
    exit();
}

session_start();
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 'guest_' . session_id(); // Create a unique guest user_id
}
$user_id = $_SESSION['user_id'];

// Fetch the user's deck from the database
$query = "SELECT c.name, d.quantity 
          FROM decks d
          JOIN card c ON d.card_id = c.id
          WHERE d.user_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param('s', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$deck = [];
while ($row = $result->fetch_assoc()) {
    $deck[] = $row;
}

echo json_encode($deck);

$stmt->close();
$conn->close();
?>
