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

// Set default filter to show all cards
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Modify the SQL query based on the filter
switch ($filter) {
    case 'monster':
        $sql = "SELECT card_name FROM monster";
        break;
    case 'spell':
        $sql = "SELECT card_name FROM spell";
        break;
    case 'trap':
        $sql = "SELECT card_name FROM trap";
        break;
    default:
        $sql = "
            SELECT card_name FROM monster
            UNION ALL
            SELECT card_name FROM spell
            UNION ALL
            SELECT card_name FROM trap
        ";
        break;
}

// Execute query
$result = $conn->query($sql);

// Start HTML output
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cards</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Cards</h1>
        <nav>
        <ul>
        <li><a href="index.php">Home</a></li>
            <li><a href="about.php">About Builder</a></li>
            <li><a href="test.php">Cards</a></li>
        </ul>
    </nav>
    </header>
    <main>
        <section>
            <h2>Cards</h2>
            <form method="GET" action="test.php">
                <label for="filter">Filter by:</label>
                <select name="filter" id="filter">
                    <option value="all" <?= $filter == 'all' ? 'selected' : '' ?>>All</option>
                    <option value="monster" <?= $filter == 'monster' ? 'selected' : '' ?>>Monsters</option>
                    <option value="spell" <?= $filter == 'spell' ? 'selected' : '' ?>>Spells</option>
                    <option value="trap" <?= $filter == 'trap' ? 'selected' : '' ?>>Traps</option>
                </select>
                <button type="submit">Apply</button>
            </form>
            <div id="card-list">
                <?php
                if ($result->num_rows > 0) {
                    // Output data of each row
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="card-item">';
                        // Add a link to the card name that directs to `card_details.php`
                        echo '<h3><a href="card_details.php?card_name=' . urlencode($row['card_name']) . '">' . htmlspecialchars($row['card_name']) . '</a></h3>';
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