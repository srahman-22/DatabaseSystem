<?php
session_start();
$loggedIn = isset($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Me - Yugioh Deck Builder</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <h1>About Me</h1>
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
        <section id="about">
            <h2>About the Yugioh Deck Builder</h2>
            <p>The Yugioh Deck Builder is your one-stop application for building, managing, and refining your Yugioh
                decks. With its intuitive interface, you can search for cards, save decks, simulate starting hands, and
                even delete old decks you no longer need.</p>
        </section>

        <section id="features">
            <h2>Key Features</h2>
            <ul>
                <li>Create and customize your own Yugioh decks.</li>
                <li>Search for cards by type, name, or other attributes.</li>
                <li>Simulate starting hands to prepare for your duels.</li>
                <li>Manage your decks: edit, delete, or save new decks anytime.</li>
                <li>User authentication to save decks securely to your account.</li>
            </ul>
        </section>

        <section id="developer">
            <h2>About the Developer</h2>
            <p>Hi there! We are the developer behind the Yugioh Deck Builder. This project was built to provide Yugioh
                enthusiasts with an easy and efficient way to manage their decks online. As we have grown the game has become more complicated
                this is our way to help bring in old and new players back into the fold.</p>
            <p>If you have any feedback or suggestions for improvement, feel free to reach out!</p>
        </section>

        <section id="technology">
            <h2>Technology Stack</h2>
            <p>The Yugioh Deck Builder is built using the following technologies:</p>
            <ul>
                <li><strong>Backend:</strong> PHP and MySQL for data management and dynamic content.</li>
                <li><strong>Frontend:</strong> HTML, CSS, and JavaScript for a clean and interactive user interface.</li>
                <li><strong>Database:</strong> MySQL for storing user accounts, decks, and card information.</li>
            </ul>
        </section>
    </main>
</body>

</html>
