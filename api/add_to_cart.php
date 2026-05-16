<?php
session_start();
$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'];
if(!isset($_SESSION['cart'][$id])) {
    $_SESSION['cart'][$id] = ['quantity' => 1];
} else {
    $_SESSION['cart'][$id]['quantity']++;
}
$total = array_sum(array_column($_SESSION['cart'], 'quantity'));
echo json_encode(['success' => true, 'total_items' => $total]);