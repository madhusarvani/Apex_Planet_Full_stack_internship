<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo "not_logged_in";
    exit;
}

$user_id = $_SESSION['user_id'];
$new_username = trim($_POST['username']);
$new_email = trim($_POST['email']);
$password = $_POST['password'];

// Verify current password
$stmt = $conn->prepare("SELECT password_hash FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!password_verify($password, $user['password_hash'])) {
    echo "wrong_password";
    exit;
}

// Check if new username is already taken (by another user)
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
$stmt->bind_param("si", $new_username, $user_id);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    echo "username_taken";
    exit;
}

// Check if new email is already taken
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
$stmt->bind_param("si", $new_email, $user_id);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    echo "email_taken";
    exit;
}

// Update user
$update = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
$update->bind_param("ssi", $new_username, $new_email, $user_id);
if ($update->execute()) {
    $_SESSION['username'] = $new_username; // update session
    echo "success";
} else {
    echo "error";
}
?>