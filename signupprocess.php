<?php
// Start session
session_start();

// Database connection
$servername = "localhost";
$username = "mhussain7";
$password = "mhussain7";
$dbname = "mhussain7";
$table = "yugiohusers";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // No hashing for password as agreed
    $plaintext_password = $password;

    // Insert user into database
    $stmt = $conn->prepare("INSERT INTO $table (username, password) VALUES (?, ?)");
    $stmt->bind_param('ss', $username, $plaintext_password);

    if ($stmt->execute()) {
        echo "<p>Account created successfully. You can now <a href='login.php'>log in</a>.</p>";
    } else {
        echo "<p>Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

$conn->close();
?>

