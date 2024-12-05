<?php
session_start();
$loggedIn = isset($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yugioh Deck Builder</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <h1>Yugioh Deck Builder</h1>
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
        <section id="welcome">
            <h2> Welcome to the Yugioh Deck Builder</h2>
            <p>Build, search cards, and save Decks that you want and get your game on</p>
        </section>
    </main>
</body>

</html>