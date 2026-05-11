<?php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['user_id'])) {
    echo json_encode([
        'logged_in' => true,
        'username' => $_SESSION['username'],
        'member_since' => date('F j, Y'), // replace with DB created_at if needed
        'session_id' => session_id()
    ]);
} else {
    echo json_encode(['logged_in' => false]);
}
?>