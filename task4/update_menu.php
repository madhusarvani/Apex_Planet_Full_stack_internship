<?php
require_once 'config/db.php';

// Clear existing menu items (optional – comment if you want to keep)
$pdo->exec("DELETE FROM menu_items");

$dishes = [
    // Indian
    ['Butter Chicken', 'Tender chicken in creamy tomato gravy', 12.99, 1, 'https://images.pexels.com/photos/2338407/pexels-photo-2338407.jpeg'],
    ['Paneer Butter Masala', 'Cottage cheese in rich buttery gravy', 11.49, 1, 'https://images.pexels.com/photos/1279330/pexels-photo-1279330.jpeg'],
    ['Chicken Biryani', 'Aromatic basmati rice with spiced chicken', 13.99, 1, 'https://images.pexels.com/photos/1639557/pexels-photo-1639557.jpeg'],
    ['Veg Biryani', 'Fragrant rice with mixed vegetables', 10.99, 1, 'https://images.pexels.com/photos/6604879/pexels-photo-6604879.jpeg'],
    ['Dal Makhani', 'Creamy black lentils cooked overnight', 8.99, 1, 'https://images.pexels.com/photos/674574/pexels-photo-674574.jpeg'],
    ['Tandoori Chicken', 'Charcoal grilled chicken with spices', 14.49, 1, 'https://images.pexels.com/photos/6071800/pexels-photo-6071800.jpeg'],
    ['Naan', 'Soft buttered flatbread', 2.49, 1, 'https://images.pexels.com/photos/958545/pexels-photo-958545.jpeg'],
    
    // Italian
    ['Margherita Pizza', 'Classic cheese and basil', 9.99, 2, 'https://images.pexels.com/photos/2147491/pexels-photo-2147491.jpeg'],
    ['Pepperoni Pizza', 'Spicy pepperoni and mozzarella', 11.99, 2, 'https://images.pexels.com/photos/2762942/pexels-photo-2762942.jpeg'],
    ['Spaghetti Carbonara', 'Egg, parmesan, pancetta', 13.49, 2, 'https://images.pexels.com/photos/1279330/pexels-photo-1279330.jpeg'],
    ['Fettuccine Alfredo', 'Creamy parmesan sauce', 12.99, 2, 'https://images.pexels.com/photos/6475199/pexels-photo-6475199.jpeg'],
    ['Lasagna', 'Layers of meat, cheese, béchamel', 14.99, 2, 'https://images.pexels.com/photos/5945567/pexels-photo-5945567.jpeg'],
    ['Garlic Bread', 'Toasted baguette with garlic butter', 4.49, 2, 'https://images.pexels.com/photos/764945/pexels-photo-764945.jpeg'],
    
    // Burgers & Sandwiches
    ['Classic Beef Burger', '100% beef with lettuce, tomato, cheese', 8.99, 3, 'https://images.pexels.com/photos/1639562/pexels-photo-1639562.jpeg'],
    ['Chicken Burger', 'Grilled chicken filet with mayo', 7.99, 3, 'https://images.pexels.com/photos/2641886/pexels-photo-2641886.jpeg'],
    ['Veggie Burger', 'Plant-based patty with avocado', 9.49, 3, 'https://images.pexels.com/photos/1279330/pexels-photo-1279330.jpeg'],
    ['Crispy Chicken Sandwich', 'Fried chicken, slaw, spicy sauce', 8.49, 3, 'https://images.pexels.com/photos/699953/pexels-photo-699953.jpeg'],
    
    // Chinese
    ['Hakka Noodles', 'Stir-fried noodles with vegetables', 9.99, 4, 'https://images.pexels.com/photos/4061520/pexels-photo-4061520.jpeg'],
    ['Chilli Chicken', 'Spicy chicken with bell peppers', 11.99, 4, 'https://images.pexels.com/photos/2762942/pexels-photo-2762942.jpeg'],
    ['Fried Rice', 'Egg fried rice with spring onions', 8.49, 4, 'https://images.pexels.com/photos/6604879/pexels-photo-6604879.jpeg'],
    ['Spring Rolls', 'Crispy rolls with vegetables', 5.99, 4, 'https://images.pexels.com/photos/1639557/pexels-photo-1639557.jpeg'],
    
    // Desserts
    ['Gulab Jamun', 'Soft milk dumplings in sugar syrup', 4.99, 5, 'https://images.pexels.com/photos/5709393/pexels-photo-5709393.jpeg'],
    ['Chocolate Lava Cake', 'Warm chocolate with molten center', 6.49, 5, 'https://images.pexels.com/photos/3026804/pexels-photo-3026804.jpeg'],
    ['Cheesecake', 'New York style strawberry cheesecake', 5.99, 5, 'https://images.pexels.com/photos/792381/pexels-photo-792381.jpeg'],
    ['Ice Cream Sundae', 'Vanilla ice cream with chocolate sauce', 4.99, 5, 'https://images.pexels.com/photos/3026804/pexels-photo-3026804.jpeg'],
    
    // Beverages
    ['Mango Lassi', 'Sweet yogurt mango drink', 3.49, 6, 'https://images.pexels.com/photos/5414725/pexels-photo-5414725.jpeg'],
    ['Masala Chai', 'Spiced Indian tea', 2.49, 6, 'https://images.pexels.com/photos/312418/pexels-photo-312418.jpeg'],
    ['Cold Coffee', 'Iced coffee with milk', 3.99, 6, 'https://images.pexels.com/photos/312418/pexels-photo-312418.jpeg'],
    ['Fresh Lime Soda', 'Refreshing lime with soda', 2.99, 6, 'https://images.pexels.com/photos/5414725/pexels-photo-5414725.jpeg'],
];

// Insert into menu_items (category_id assumed from category names)
// First map category names to IDs
$catMap = [];
$cats = $pdo->query("SELECT id, name FROM categories")->fetchAll();
foreach($cats as $c) {
    $catMap[strtolower($c['name'])] = $c['id'];
}
// For simplicity, assign categories: 1=Indian,2=Italian,3=Burgers,4=Chinese,5=Desserts,6=Beverages
// but we'll use the arrays directly with category_id numbers
$stmt = $pdo->prepare("INSERT INTO menu_items (name, description, price, category_id, image) VALUES (?,?,?,?,?)");
foreach($dishes as $dish) {
    $stmt->execute([$dish[0], $dish[1], $dish[2], $dish[3], $dish[4]]);
}
echo "✅ " . count($dishes) . " delicious items added with professional images!";
?>