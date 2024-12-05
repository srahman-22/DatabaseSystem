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

// Get the card name from the URL
$card_name = isset($_GET['card_name']) ? $_GET['card_name'] : '';

// If no card is specified, redirect back to the Deck Builder page
if (empty($card_name)) {
    header("Location: test.php");
    exit();
}

// Query for card details based on type
$card_details = [];
$card_type = '';

$sql = "
    SELECT card_name, subtype, attribute, effect_type, atk, def, level, 'Monster' as type
    FROM monster
    WHERE card_name = ?
    UNION ALL
    SELECT card_name, subtype, NULL as attribute, NULL as effect_type, NULL as atk, NULL as def, NULL as level, 'Spell' as type
    FROM spell
    WHERE card_name = ?
    UNION ALL
    SELECT card_name, subtype, NULL as attribute, NULL as effect_type, NULL as atk, NULL as def, NULL as level, 'Trap' as type
    FROM trap
    WHERE card_name = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $card_name, $card_name, $card_name);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $card_details = $result->fetch_assoc();
    $card_type = $card_details['type'];
} else {
    echo "<p>Card details not found.</p>";
    exit();
}

// Start HTML output
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Card Details - <?= htmlspecialchars($card_name) ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Card Details</h1>
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
            <h2><?= htmlspecialchars($card_name) ?></h2>
            <p><strong>Type:</strong> <?= htmlspecialchars($card_type) ?></p>
            <p><strong>Subtype:</strong> <?= htmlspecialchars($card_details['subtype']) ?></p>

            <?php if ($card_type == 'Monster') : ?>
                <p><strong>Attribute:</strong> <?= htmlspecialchars($card_details['attribute']) ?></p>
                <p><strong>Effect Type:</strong> <?= htmlspecialchars($card_details['effect_type'] ?: 'None') ?></p>
                <p><strong>Attack:</strong> <?= htmlspecialchars($card_details['atk']) ?></p>
                <p><strong>Defense:</strong> <?= htmlspecialchars($card_details['def']) ?></p>
                <p><strong>Level:</strong> <?= htmlspecialchars($card_details['level']) ?></p>
            <?php endif; ?>
        </section>

        <?php if ($card_type == 'Monster') : ?>
            <section>
                <h3>Fusion and Ritual Details</h3>
                <?php
                // Query for fusion details
                $fusion_sql = "
                    SELECT fm.fusionMaterial, fm.quantity
                    FROM fusionMon fm
                    WHERE fm.fusionMonster = ?
                ";
                $fusion_stmt = $conn->prepare($fusion_sql);
                $fusion_stmt->bind_param("s", $card_name);
                $fusion_stmt->execute();
                $fusion_result = $fusion_stmt->get_result();

                if ($fusion_result->num_rows > 0) {
                    echo "<h4>Fusion Materials</h4>";
                    while ($row = $fusion_result->fetch_assoc()) {
                        echo "<p>" . htmlspecialchars($row['fusionMaterial']) . " x" . htmlspecialchars($row['quantity']) . "</p>";
                    }
                }

                // Query for ritual details
                $ritual_sql = "
                    SELECT r.ritualSpell
                    FROM rituals r
                    WHERE r.ritualMonster = ?
                ";
                $ritual_stmt = $conn->prepare($ritual_sql);
                $ritual_stmt->bind_param("s", $card_name);
                $ritual_stmt->execute();
                $ritual_result = $ritual_stmt->get_result();

                if ($ritual_result->num_rows > 0) {
                    echo "<h4>Ritual Requirements</h4>";
                    while ($row = $ritual_result->fetch_assoc()) {
                        echo "<p>Requires Ritual Spell: " . htmlspecialchars($row['ritualSpell']) . "</p>";
                    }
                }
                ?>
            </section>
        <?php endif; ?>
    </main>
</body>
</html>
<?php
// Close connection
$conn->close();
?>
