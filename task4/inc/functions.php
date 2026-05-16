<?php
function isLoggedIn() { return isset($_SESSION['user_id']); }
function isAdmin() { return ($_SESSION['role'] ?? '') === 'admin'; }
function redirect($url) { header("Location: $url"); exit; }

function getCartCount() {
    $cart = $_SESSION['cart'] ?? [];
    return array_sum(array_column($cart, 'quantity'));
}

function getCartItems() {
    return $_SESSION['cart'] ?? [];
}
?>