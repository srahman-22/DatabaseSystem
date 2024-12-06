<?php
session_start();

if (!isset($_SESSION['username'])) {
    echo "<p>You must be logged in to view your decks. <a href='login.php'>Login here</a>.</p>";
    exit();
}

// Database connection
$host = "localhost";
$username = "mhussain7";
$password = "mhussain7";
$dbname = "mhussain7";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the logged-in username
$loggedInUser = $_SESSION['username'];

// Get the user's decks
$sql = "SELECT d.deck_id, d.cards FROM decks d 
        INNER JOIN yugiohusers u ON d.user_id = u.id 
        WHERE u.username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $loggedInUser);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Decks</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* CSS for horizontal button alignment */
        .button-container {
            display: flex;
            gap: 10px; /* Add space between buttons */
        }
        .button-container button,
        .button-container form {
            margin: 0; /* Remove default margins */
        }
    </style>
    <script>
        // JavaScript to handle deck deletion
        function deleteDeck(deckId) {
            fetch('delete_deck.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ deck_id: deckId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message); // Show success message
                    document.getElementById(`deck-container-${deckId}`).remove(); // Remove the deck from the page
                } else {
                    alert(data.message || "An error occurred while deleting the deck.");
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert("An unexpected error occurred.");
            });
        }

        // JavaScript for handling the Starting Hand button
        function drawStartingHand(deckId) {
            fetch(`starting_hand.php?deck_id=${deckId}`)
                .then(response => response.json())
                .then(data => {
                    const startingHandArea = document.getElementById('starting-hand');
                    startingHandArea.innerHTML = ""; // Clear previous results

                    if (data.success) {
                        const cards = data.cards.map(card => `<li>${card}</li>`).join("");
                        startingHandArea.innerHTML = `
                            <h3>Starting Hand:</h3>
                            <ul>${cards}</ul>
                        `;
                    } else {
                        startingHandArea.innerHTML = `
                            <p class="error">${data.message || "An error occurred while drawing the starting hand."}</p>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert("An unexpected error occurred.");
                });
        }
    </script>
</head>
<body>
    <header>
        <h1>Your Decks</h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="deckbuilder.php">Deck Builder</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <section>
            <?php if ($result->num_rows > 0): ?>
                <h2>Your Saved Decks</h2>
                <div id="starting-hand" class="starting-hand-area"></div> <!-- Area to show starting hand -->
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="deck-container" id="deck-container-<?= htmlspecialchars($row['deck_id']) ?>">
                        <h3>Deck ID: <?= htmlspecialchars($row['deck_id']) ?></h3>
                        <table class="deck-table">
                            <thead>
                                <tr>
                                    <th>Card Name</th>
                                    <th>Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $cards = json_decode($row['cards'], true);
                                if (is_array($cards)):
                                    foreach ($cards as $cardName => $cardDetails): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($cardName) ?></td>
                                            <td><?= htmlspecialchars($cardDetails['quantity']) ?></td>
                                        </tr>
                                    <?php endforeach;
                                else: ?>
                                    <tr>
                                        <td colspan="2">No cards found in this deck.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <div class="button-container">
                            <form method="GET" action="deckbuilder.php">
                                <input type="hidden" name="deck_id" value="<?= htmlspecialchars($row['deck_id']) ?>">
                                <button type="submit">Edit Deck</button>
                            </form>
                            <button onclick="drawStartingHand(<?= htmlspecialchars($row['deck_id']) ?>)">Starting Hand</button>
                            <button onclick="deleteDeck(<?= htmlspecialchars($row['deck_id']) ?>)">Delete Deck</button>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <h2>No decks found</h2>
                <p><a href="deckbuilder.php">Build your first deck here!</a></p>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>

<?php
$conn->close();
?>
