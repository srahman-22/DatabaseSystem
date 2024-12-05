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

        function saveDeck() {
            fetch('modify_deck.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ cards: selectedCards })
            })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.success) {
                        selectedCards = {};
                        renderSelectedCards();
                    }
                })
                .catch(error => console.error('Error saving deck:', error));
        }

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
    </script>
</body>

</html>
