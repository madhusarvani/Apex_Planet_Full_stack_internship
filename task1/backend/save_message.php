<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debug: see what was posted
    // file_put_contents('debug.log', print_r($_POST, true));

    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $message = mysqli_real_escape_string($conn, trim($_POST['message']));

    if (empty($name) || empty($email) || empty($message)) {
        die("All fields are required.");
    }

    $query = "INSERT INTO messages (name, email, message) VALUES ('$name', '$email', '$message')";
    if (mysqli_query($conn, $query)) {
        header("Location: ../contact.html?success=1");
        exit();
    } else {
        echo "Database error: " . mysqli_error($conn);
    }
} else {
    echo "No POST data received.";
}
?>