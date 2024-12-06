<?php
session_start();
$loggedIn = isset($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Yugioh Deck Builder</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <h1>Sign Up</h1>
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
        <section id="signup">
            <h2>Create an Account</h2>
            <form action="signupprocess.php" method="POST">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
                <br>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <br>
                <button type="submit">Sign Up</button>
            </form>
        </section>
    </main>
</body>

</html>