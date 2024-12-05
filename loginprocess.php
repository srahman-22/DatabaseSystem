<?php
session_start();

// Database connection
$servername = "localhost";
$username = "srahman22";
$password = "srahman22"; // Replace with your actual password
$dbname = "srahman22"; // Replace with your actual database name
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

    // Check the user's credentials
    $stmt = $conn->prepare("SELECT id, password FROM srahman22.yugiohusers WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();

        if ($password === $hashed_password) {
            echo "<p>Login successful! Welcome, $username.</p>";
            $_SESSION['username'] = $username;
            header('Location: welcome.php'); // Redirect to a welcome page
            exit();
        } else {
            echo "<p>Invalid credentials. Please try again.</p>";
            error_log("Login failed for user: $username", 0);
        }
    } else {
        echo "<p>Invalid credentials. Please try again.</p>";
    }
    $stmt->close();
}

$conn->close();
?>