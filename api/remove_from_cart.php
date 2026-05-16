<?php
session_start();
$data = json_decode(file_get_contents('php://input'), true);
unset($_SESSION['cart'][$data['id']]);
$total = array_sum(array_column($_SESSION['cart'], 'quantity'));
echo json_encode(['success' => true, 'total_items' => $total]);