<?php
header('Content-Type: application/json');

// Database connection
$host = "localhost";
$username = "mhussain7";
$password = "mhussain7";
$dbname = "mhussain7";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]);
    exit();
}

// Handle filtering or card details retrieval
$card_name = isset($_GET['card_name']) ? $_GET['card_name'] : null;
$type = isset($_GET['type']) ? $_GET['type'] : 'all';
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;

if ($card_name) {
    // Fetch specific card details
    $sql = "
        SELECT card_name, subtype, attribute, effect_type, atk, def, level, 'Monster' as type
        FROM monster WHERE card_name = ?
        UNION ALL
        SELECT card_name, subtype, NULL as attribute, NULL as effect_type, NULL as atk, NULL as def, NULL as level, 'Spell' as type
        FROM spell WHERE card_name = ?
        UNION ALL
        SELECT card_name, subtype, NULL as attribute, NULL as effect_type, NULL as atk, NULL as def, NULL as level, 'Trap' as type
        FROM trap WHERE card_name = ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sss', $card_name, $card_name, $card_name);
    $stmt->execute();
    $result = $stmt->get_result();
    $card_details = $result->fetch_assoc();

    if ($card_details) {
        echo json_encode(["success" => true, "card" => $card_details]);
    } else {
        echo json_encode(["success" => false, "message" => "Card not found."]);
    }
} else {
    // Filter cards
    switch ($type) {
        case 'monster':
            $sql = "SELECT card_name AS name, 'Monster' AS type FROM monster LIMIT ? OFFSET ?";
            break;
        case 'spell':
            $sql = "SELECT card_name AS name, 'Spell' AS type FROM spell LIMIT ? OFFSET ?";
            break;
        case 'trap':
            $sql = "SELECT card_name AS name, 'Trap' AS type FROM trap LIMIT ? OFFSET ?";
            break;
        default:
            $sql = "
                SELECT card_name AS name, 'Monster' AS type FROM monster
                UNION ALL
                SELECT card_name AS name, 'Spell' AS type FROM spell
                UNION ALL
                SELECT card_name AS name, 'Trap' AS type FROM trap
                LIMIT ? OFFSET ?";
            break;
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    $cards = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode(["success" => true, "cards" => $cards]);
}

$conn->close();
?>
