<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 'guest_' . session_id(); // Create a unique guest user_id
}
$loggedIn = isset($_SESSION['username']);
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

        <!-- Selected Cards Section -->
        <div id="selected-cards-section" style="margin-top: 20px;">
            <h3>Selected Cards</h3>
            <div id="selected-cards">
                <!-- Selected cards will be displayed here -->
            </div>
            <button id="save-deck">Save Deck</button>
        </div>
    </main>

    <script>
        const limit = 10;
        let offset = 0;
        let selectedCards = {}; // Temporary storage for selected cards

        // Load cards from the database
        function loadCards(filterType = '') {
            const query = `get_cards.php?limit=${limit}&offset=${offset}&type=${filterType}`;

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
                            cardItem.id = `card-${card.id}`;
                            cardItem.innerHTML = `
                                <h3>${card.name}</h3>
                                <p>Type: ${card.type}</p>
                                <button onclick="addCard(${card.id}, '${card.name}')">+</button>
                                <button onclick="removeCard(${card.id})">-</button>
                            `;
                            cardList.appendChild(cardItem);
                        });

                        offset += limit;
                        loadMoreButton.style.display = 'block';
                    } else if (offset === 0) {
                        cardList.innerHTML = '<p>No cards match your filter. Try a different selection.</p>';
                        loadMoreButton.style.display = 'none';
                    } else {
                        loadMoreButton.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error("Error loading cards:", error);
                });
        }

        // Add a card to the selected cards section
        function addCard(cardId, cardName) {
            if (selectedCards[cardId]) {
                selectedCards[cardId].quantity += 1;
            } else {
                selectedCards[cardId] = { name: cardName, quantity: 1 };
            }
            renderSelectedCards();
        }

        // Remove a card from the selected cards section
        function removeCard(cardId) {
            if (selectedCards[cardId]) {
                selectedCards[cardId].quantity -= 1;
                if (selectedCards[cardId].quantity <= 0) {
                    delete selectedCards[cardId];
                }
                renderSelectedCards();
            }
        }

        // Render the selected cards section
        function renderSelectedCards() {
            const selectedCardsDiv = document.getElementById("selected-cards");
            selectedCardsDiv.innerHTML = '';

            Object.keys(selectedCards).forEach(cardId => {
                const card = selectedCards[cardId];
                const cardItem = document.createElement("div");
                cardItem.innerHTML = `
                    <h4>${card.name}</h4>
                    <p>Quantity: ${card.quantity}</p>
                `;
                selectedCardsDiv.appendChild(cardItem);
            });
        }

        // Save the selected cards to the decks table
        function saveDeck() {
    const cards = Object.keys(selectedCards).map(cardId => ({
        card_id: parseInt(cardId),
        quantity: selectedCards[cardId].quantity
    }));

    fetch('modify_deck.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ cards })
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json(); // Parse the JSON response
        })
        .then(data => {
            if (data.success) {
                alert(data.message); // Show success message
                selectedCards = {}; // Clear selected cards
                renderSelectedCards();
            } else {
                alert(data.message); // Show error message
            }
        })
        .catch(error => {
            console.error("Error saving deck:", error);
            alert("An error occurred while saving the deck. Please try again.");
        });
}




        // Event listeners
        document.getElementById("apply-filter").addEventListener("click", () => {
            const filterType = document.getElementById("type-filter").value;
            offset = 0;
            loadCards(filterType);
        });

        document.getElementById("load-more").addEventListener("click", () => {
            const filterType = document.getElementById("type-filter").value;
            loadCards(filterType);
        });

        document.getElementById("save-deck").addEventListener("click", saveDeck);

        // Initial load
        loadCards();
    </script>
</body>

</html>
