<?php
session_start();
require_once '../config/db.php';
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}
$data = json_decode(file_get_contents('php://input'), true);
$item_id = $data['id'] ?? 0;
$meal_type = $data['meal_type'] ?? 'lunch';
$user_id = $_SESSION['user_id'];
$today = date('Y-m-d');
$stmt = $pdo->prepare("INSERT INTO meal_logs (user_id, menu_item_id, log_date, meal_type) VALUES (?,?,?,?)");
$result = $stmt->execute([$user_id, $item_id, $today, $meal_type]);
echo json_encode(['success' => $result]);
?>