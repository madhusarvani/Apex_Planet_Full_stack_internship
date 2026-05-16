<?php
require_once 'config/db.php';
for ($i = 1; $i <= 100; $i++) {
    $name = "Dish $i";
    $desc = "Delicious item number $i";
    $price = rand(5, 25) . '.' . rand(0, 99);
    $cat = rand(1, 7);
    $stmt = $pdo->prepare("INSERT INTO menu_items (name, description, price, category_id, image) VALUES (?,?,?,?, 'default.jpg')");
    $stmt->execute([$name, $desc, $price, $cat]);
}
echo "100 items added!";
?>