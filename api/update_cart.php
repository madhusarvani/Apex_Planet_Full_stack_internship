<?php
session_start();
$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'];
$qty = (int)$data['quantity'];
if($qty <= 0) unset($_SESSION['cart'][$id]);
else $_SESSION['cart'][$id]['quantity'] = $qty;
$total = array_sum(array_column($_SESSION['cart'], 'quantity'));
echo json_encode(['success' => true, 'total_items' => $total]);