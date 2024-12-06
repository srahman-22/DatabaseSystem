<?php
session_start();
$loggedIn = isset($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Yugioh Deck Builder</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
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
        <section id="welcome">
            <h2>Welcome to the Yugioh Deck Builder Application</h2>
            <p>Feel free to explore and create your ultimate deck!</p>
        </section>
    </main>
</body>
</html>
