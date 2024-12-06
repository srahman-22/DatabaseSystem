<?php
session_start();
$loggedIn = isset($_SESSION['username']) && !empty($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deck Builder</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <h1>Yu-Gi-Oh Deck Builder</h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="deckbuilder.php">Deck Builder</a></li>
                <?php if ($loggedIn): ?>
                    <li><a href="logout.php">Logout</a></li>
                    <li><a href="display_user_deck.php">My Decks</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <section>
            <h2>Build Your Deck</h2>

            <!-- Filter Section -->
            <div id="filter-section">
                <label for="type-filter">Filter by Type:</label>
                <select name="type" id="type-filter">
                    <option value="all">All</option>
                    <option value="monster">Monster</option>
                    <option value="spell">Spell</option>
                    <option value="trap">Trap</option>
                </select>
                <button id="apply-filter">Apply Filter</button>
            </div>

            <!-- Card List Section -->
            <div id="card-list"></div>
            <button id="load-more">Load More</button>
        </section>
                
        <section>
            <!-- Selected Cards Section -->
            <h3>Your Selected Cards</h3>
            <div id="selected-cards"></div>
            <button id="save-deck">Save Deck</button>
        </section>
    </main>

    <script>
        const selectedCards = {};
        const limit = 10;
        let offset = 0;
        const urlParams = new URLSearchParams(window.location.search);
        const deckId = urlParams.get('deck_id'); // Get deck_id from the query parameter

        // Load cards dynamically
        function loadCards(type = 'all') {
            fetch(`get_cards.php?type=${type}&limit=${limit}&offset=${offset}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.cards.length > 0) {
                        const cardList = document.getElementById('card-list');
                        data.cards.forEach(card => {
                            const cardDiv = document.createElement('div');
                            cardDiv.classList.add('card-item');
                            cardDiv.innerHTML = `
                                <h3>${card.name}</h3>
                                <p>Type: ${card.type}</p>
                                <button class="add-card">+</button>
                                <button class="remove-card">-</button>
                                <button class="view-card">View Details</button>
                            `;

                            cardDiv.querySelector('.add-card').addEventListener('click', () => addCard(card));
                            cardDiv.querySelector('.remove-card').addEventListener('click', () => removeCard(card));
                            cardDiv.querySelector('.view-card').addEventListener('click', () => {
                                window.location.href = `card_details.php?card_name=${encodeURIComponent(card.name)}`;
                            });

                            cardList.appendChild(cardDiv);
                        });
                        offset += limit;
                    } else {
                        document.getElementById('load-more').style.display = 'none';
                    }
                })
                .catch(error => console.error('Error loading cards:', error));
        }

        // Preload deck if editing
        function preloadDeck() {
    if (!deckId) return;

    fetch(`get_deck.php?deck_id=${deckId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.cards) {
                const cardNames = Object.keys(data.cards);

                if (cardNames.length === 0) {
                    console.error("No cards found in the deck.");
                    return;
                }

                // Fetch card details for preloading
                fetch('get_cards.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ names: cardNames })
                })
                    .then(response => response.json())
                    .then(detailsData => {
                        if (detailsData.success && detailsData.cards) {
                            detailsData.cards.forEach(card => {
                                if (data.cards[card.name]) {
                                    selectedCards[card.name] = {
                                        ...card,
                                        quantity: data.cards[card.name].quantity
                                    };
                                }
                            });
                            renderSelectedCards();
                        } else {
                            console.error('Failed to fetch card details:', detailsData.message);
                        }
                    })
                    .catch(error => console.error('Error fetching card details:', error));
            } else {
                console.error('Failed to preload deck:', data.message);
            }
        })
        .catch(error => console.error('Error preloading deck:', error));
}


        // Add a card to the selected list
        function addCard(card) {
            if (selectedCards[card.name]) {
                if (selectedCards[card.name].quantity < 3) {
                    selectedCards[card.name].quantity++;
                } else {
                    alert('You cannot add more than 3 of the same card.');
                }
            } else {
                selectedCards[card.name] = { ...card, quantity: 1 };
            }
            renderSelectedCards();
        }

        // Remove a card from the selected list
        function removeCard(card) {
            if (selectedCards[card.name]) {
                if (selectedCards[card.name].quantity > 1) {
                    selectedCards[card.name].quantity--;
                } else {
                    delete selectedCards[card.name];
                }
                renderSelectedCards();
            }
        }

        // Render selected cards
        function renderSelectedCards() {
            const selectedSection = document.getElementById('selected-cards');
            selectedSection.innerHTML = '';
            for (const cardName in selectedCards) {
                const card = selectedCards[cardName];
                const cardDiv = document.createElement('div');
                cardDiv.classList.add('selected-card');
                cardDiv.innerHTML = `${card.name} x${card.quantity}`;
                selectedSection.appendChild(cardDiv);
            }
        }

        // Save deck
        function saveDeck() {
    console.log("Attempting to save deck..."); // Debug message

    const endpoint = 'modify_deck.php';
    const payload = {
        cards: selectedCards,
        ...(deckId && { deck_id: deckId }) // Include deck_id if editing an existing deck
    };

    fetch(endpoint, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log("Backend response:", data); // Log backend response
            if (data.success) {
                alert(data.message); // Show success message
                Object.keys(selectedCards).forEach(key => delete selectedCards[key]);
                renderSelectedCards(); // Clear UI
            } else {
                alert('Failed to save deck: ' + data.message); // Show error
            }
        })
        .catch(error => {
            console.error('Error saving deck:', error);
            alert('An unexpected error occurred while saving the deck.');
        });
}



        // Event listeners
        document.getElementById('apply-filter').addEventListener('click', () => {
            offset = 0;
            document.getElementById('card-list').innerHTML = '';
            const type = document.getElementById('type-filter').value;
            loadCards(type);

            const loadMoreButton = document.getElementById('load-more');
            loadMoreButton.style.display = 'block';
        });

        document.getElementById('load-more').addEventListener('click', () => {
            const type = document.getElementById('type-filter').value;
            loadCards(type);
        });

        document.getElementById('save-deck').addEventListener('click', saveDeck);

        // Initial load
        loadCards();
        preloadDeck(); // Preload the deck if editing
    </script>
</body>

</html>
