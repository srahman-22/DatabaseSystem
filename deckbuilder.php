<?php
session_start();
$loggedIn = isset($_SESSION['username']);
if (!$loggedIn) {
    // Simulate a guest user session
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['user_id'] = 'guest_' . session_id();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deck Builder - Yugioh Deck Builder</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <h1>Deck Builder</h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About Me</a></li>
                <li><a href="deckbuilder.php">Deck Builder</a></li>
                <?php if ($loggedIn): ?>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="signup.php">Sign Up</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <h2>Build Your Deck</h2>

        <!-- Filter Section -->
        <div id="filter-section">
            <label for="type-filter">Filter by Type:</label>
            <select id="type-filter">
                <option value="">All</option>
                <option value="Monster">Monster</option>
                <option value="Spell">Spell</option>
                <option value="Trap">Trap</option>
            </select>
            <button id="apply-filter">Apply Filter</button>
        </div>

        <div id="card-list">
            <!-- Cards will be displayed here -->
        </div>
        <button id="load-more">Load More</button>

        <!-- Add and Remove Buttons -->
        <div id="card-actions" style="margin-top: 20px; text-align: center;">
            <button id="add-card">+</button>
            <button id="remove-card">-</button>
        </div>

        <!-- Deck Section -->
        <div id="deck-section" style="margin-top: 20px;">
            <h3>Your Deck</h3>
            <div id="deck-list">
                <!-- Deck cards will be displayed here -->
            </div>
        </div>
    </main>

    <script>
        const limit = 10;
        let offset = 0;
        let selectedCard = null; // Keep track of the currently selected card

        function selectCard(cardId) {
            // Deselect the currently selected card
            if (selectedCard) {
                selectedCard.classList.remove('selected');
            }

            // Highlight the new selected card
            const newCard = document.getElementById(`card-${cardId}`);
            newCard.classList.add('selected');

            // Update the selected card
            selectedCard = newCard;

            console.log(`Card ${cardId} selected`);
        }

        function modifyCard(action) {
            if (!selectedCard) {
                alert("Please select a card first.");
                return;
            }

            const cardId = selectedCard.id.split('-')[1]; // Extract card ID from the element's ID
            const query = `modify_deck.php?action=${action}&card_id=${cardId}`;

            fetch(query)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        loadDeck(); // Reload the deck to reflect changes
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error("Error modifying deck:", error);
                });
        }

        function loadCards(filterType = '') {
            const query = `get_cards.php?limit=${limit}&offset=${offset}&type=${filterType}`;
            console.log('Fetching:', query);

            fetch(query)
                .then(response => response.json())
                .then(data => {
                    const cardList = document.getElementById("card-list");
                    const loadMoreButton = document.getElementById("load-more");

                    if (offset === 0) {
                        cardList.innerHTML = ''; // Clear current cards only on new filter
                    }

                    if (data.length > 0) {
                        data.forEach(card => {
                            const cardItem = document.createElement("div");
                            cardItem.classList.add("card-item");
                            cardItem.id = `card-${card.id}`; // Add unique ID for each card
                            cardItem.onclick = () => selectCard(card.id); // Set click event
                            cardItem.innerHTML = `
                                <h3>${card.name}</h3>
                                <p>Type: ${card.type}</p>
                                <p>Quantity: ${card.quantity}</p>
                            `;
                            cardList.appendChild(cardItem);
                        });

                        offset += limit;
                        loadMoreButton.style.display = 'block'; // Show the button if there are more cards
                    } else if (offset === 0) {
                        // No cards found on a new filter
                        cardList.innerHTML = '<p>No cards match your filter. Try a different selection.</p>';
                        loadMoreButton.style.display = 'none'; // Hide the button
                    } else {
                        // No more cards to load
                        loadMoreButton.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error("Error loading cards:", error);
                    document.getElementById("card-list").innerHTML = "<p>Failed to load cards. Please try again later.</p>";
                });
        }

        function loadDeck() {
    fetch('get_deck.php') // Use a separate PHP script to fetch the deck data
        .then(response => response.json())
        .then(data => {
            const deckList = document.getElementById("deck-list");

            if (data.length > 0) {
                deckList.innerHTML = ''; // Clear existing deck content

                data.forEach(card => {
                    const deckItem = document.createElement("div");
                    deckItem.classList.add("deck-item");
                    deckItem.innerHTML = `
                        <h4>${card.name}</h4>
                        <p>Quantity: ${card.quantity}</p>
                    `;
                    deckList.appendChild(deckItem);
                });
            } else {
                deckList.innerHTML = '<p>Your deck is empty.</p>';
            }
        })
        .catch(error => {
            console.error("Error loading deck:", error);
        });
}


        // Apply Filter Button Event
        document.getElementById("apply-filter").addEventListener("click", () => {
            const filterType = document.getElementById("type-filter").value;
            offset = 0; // Reset offset when applying a new filter
            loadCards(filterType);
        });

        // Load More Button Event
        document.getElementById("load-more").addEventListener("click", () => {
            const filterType = document.getElementById("type-filter").value;
            loadCards(filterType);
        });

        // Add and Remove Button Events
        document.getElementById("add-card").addEventListener("click", () => modifyCard('add'));
        document.getElementById("remove-card").addEventListener("click", () => modifyCard('remove'));

        // Initial load of cards and deck
        loadCards();
        loadDeck();
    </script>
</body>

</html>
