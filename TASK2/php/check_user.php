<?php
header('Content-Type: application/json');
require_once 'config.php';

$type = $_GET['type'] ?? '';   // 'username' or 'email'
$value = $_GET['value'] ?? '';

$response = ['exists' => false];

if ($type === 'username') {
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $value);
    $stmt->execute();
    $stmt->store_result();
    $response['exists'] = $stmt->num_rows > 0;
    $stmt->close();
} elseif ($type === 'email') {
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $value);
    $stmt->execute();
    $stmt->store_result();
    $response['exists'] = $stmt->num_rows > 0;
    $stmt->close();
}

echo json_encode($response);
?>