<?php
session_start();

// Database connection
$servername = "localhost";
$username = "srahman22";
$password = "srahman22"; // Replace with your actual password
$dbname = "srahman22"; // Replace with your actual database name


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    echo "<p>You must be logged in to view your deck.</p>";
    exit;
}

$username = $_SESSION['username'];

// Find the user_id using the username
$sql_user = "SELECT id FROM yugiohusers WHERE username = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("s", $username);
$stmt_user->execute();
$stmt_user->store_result();
$stmt_user->bind_result($user_id);

if ($stmt_user->num_rows > 0) {
    $stmt_user->fetch();
    $stmt_user->close();

    // Handle card removal
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_card'])) {
        $card_to_remove = $_POST['remove_card'];

        // Fetch the user's current deck
        $sql_fetch_deck = "SELECT cards FROM decks WHERE user_id = ?";
        $stmt_fetch_deck = $conn->prepare($sql_fetch_deck);
        $stmt_fetch_deck->bind_param("i", $user_id);
        $stmt_fetch_deck->execute();
        $stmt_fetch_deck->store_result();
        $stmt_fetch_deck->bind_result($cards_json);

        if ($stmt_fetch_deck->num_rows > 0) {
            $stmt_fetch_deck->fetch();
            $deck = json_decode($cards_json, true);

            // Reduce the quantity of the card or remove it if quantity is 1
            if (isset($deck[$card_to_remove])) {
                $deck[$card_to_remove]['quantity']--;
                if ($deck[$card_to_remove]['quantity'] <= 0) {
                    unset($deck[$card_to_remove]);
                }

                // Update the deck in the database
                $updated_deck_json = json_encode($deck);
                $sql_update_deck = "UPDATE decks SET cards = ? WHERE user_id = ?";
                $stmt_update_deck = $conn->prepare($sql_update_deck);
                $stmt_update_deck->bind_param("si", $updated_deck_json, $user_id);
                $stmt_update_deck->execute();
                $stmt_update_deck->close();
            }
        }
        $stmt_fetch_deck->close();
    }

    // Fetch the updated deck
    $sql_deck = "SELECT cards FROM decks WHERE user_id = ?";
    $stmt_deck = $conn->prepare($sql_deck);
    $stmt_deck->bind_param("i", $user_id);
    $stmt_deck->execute();
    $stmt_deck->store_result();
    $stmt_deck->bind_result($cards_json);

    if ($stmt_deck->num_rows > 0) {
        $stmt_deck->fetch();
        $cards = json_decode($cards_json, true);

        if ($cards) {
            $deckOutput = "<h2>Your Deck:</h2><ul>";
            foreach ($cards as $card_name => $card_info) {
                $deckOutput .= "<li>" . htmlspecialchars($card_info['name']) . 
                " (x" . $card_info['quantity'] . ") - " . htmlspecialchars($card_info['type']) . "
                <form method='POST' style='display:inline;'>
                    <button type='submit' name='remove_card' value='" . htmlspecialchars($card_name) . "'>Remove</button>
                </form></li>";
            }
            $deckOutput .= "</ul>";
        } else {
            $deckOutput = "<p>Your deck is empty.</p>";
        }
    } else {
        $deckOutput = "<p>No deck found.</p>";
    }
    $stmt_deck->close();
} else {
    $deckOutput = "<p>Invalid user. Please log in again.</p>";
    $stmt_user->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Saved Deck</title>
<link rel="stylesheet" href="style.css">
</head>

<body>
<header>
    <h1>Yu-Gi-Oh Deck Builder</h1>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="about.php">About Builder</a></li>
            <li><a href="deckbuilder.php">Deck Builder</a></li>
            <?php if ($loggedIn): ?>
                <li><a href="logout.php">Logout</a></li>
                <li><a href="display_user_deck.php">Saved Deck</a></li>
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="signup.php">Sign Up</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<main>
    <section>
        <?php echo $deckOutput; ?>
    </section>
</main>
</body>

</html>