<?php
session_start();
$loggedIn = isset($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Yugioh Deck Builder</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <h1>About Yugioh Deck Builder</h1>
        <nav>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="about.php">About Builder</a></li>
        <li><a href="deckbuilder.php">Deck Builder</a></li>
        <?php if (isset($_SESSION['username'])): ?>
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
        <section id="about">
            <h2>About the Yugioh Deck Builder</h2>
            <p>This application allows users to create, search, and manage Yugioh decks. Start building your ultimate
                deck and prepare to duel!</p>
        </section>
    </main>
</body>

</html>