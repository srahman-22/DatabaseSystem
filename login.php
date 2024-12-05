<?php
session_start();

// Check if the user is already logged in
if (isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Yugioh Deck Builder</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Login</h1>
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
        <section id="login">
            <h2>Login</h2>
            <form action="loginprocess.php" method="POST">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
                <br>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <br>
                <button type="submit">Login</button>
            </form>
            <p>Don't have an account? <a href="signup.php">Sign up here</a>.</p>
        </section>
    </main>
</body>
</html>