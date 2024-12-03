<?php
// Database connection parameters
$servername = "localhost";
$username = "srahman22";
$password = "srahman22"; // Replace with your actual password
$dbname = "srahman22"; // Replace with your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to fetch card names from all three tables
$sql = "
    SELECT card_name FROM monster
    UNION ALL
    SELECT card_name FROM spell
    UNION ALL
    SELECT card_name FROM trap
";

// Execute query
$result = $conn->query($sql);

// Start HTML output
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
        <h1>Deck Builder</h1>
        <nav>
    <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="about.php">About Me</a></li>
            <li><a href="test.php">Deck Builder</a></li>
    </ul>
</nav>

    </header>
    
    <main>
        <section>
            <h2>Cards</h2>
            <div id="card-list">
                <?php
                if ($result->num_rows > 0) {
                    // Output data of each row
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="card-item">';
                        echo '<h3>' . htmlspecialchars($row['card_name']) . '</h3>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>No cards found.</p>';
                }
                ?>
            </div>
        </section>
    </main>
</body>
</html>
<?php
// Close connection
$conn->close();
?>

