<?php
require_once 'config/db.php';

// Food items with Unsplash image URLs
$menuItems = [
    [
        'name' => 'Margherita Pizza',
        'description' => 'Classic Italian pizza with fresh mozzarella, basil, and tomato sauce. A timeless favorite!',
        'price' => 12.99,
        'category' => 'Pizza',
        'image' => 'https://images.unsplash.com/photo-1585238341710-4913d3ca7c0d?w=400&h=300&fit=crop'
    ],
    [
        'name' => 'Pepperoni Pizza',
        'description' => 'Delicious pizza loaded with fresh pepperoni and melted mozzarella cheese',
        'price' => 14.99,
        'category' => 'Pizza',
        'image' => 'https://images.unsplash.com/photo-1628840042765-356cda07f4ee?w=400&h=300&fit=crop'
    ],
    [
        'name' => 'Veggie Deluxe Pizza',
        'description' => 'Fresh vegetables including bell peppers, onions, mushrooms, and olives',
        'price' => 13.99,
        'category' => 'Pizza',
        'image' => 'https://images.unsplash.com/photo-1511689534300-1c1ef2d93688?w=400&h=300&fit=crop'
    ],
    [
        'name' => 'Classic Burger',
        'description' => 'Juicy beef patty with lettuce, tomato, cheese, and special sauce on a toasted bun',
        'price' => 9.99,
        'category' => 'Burgers',
        'image' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=400&h=300&fit=crop'
    ],
    [
        'name' => 'Cheese Burger',
        'description' => 'Double cheese melted on a perfectly cooked beef patty with crispy fries',
        'price' => 10.99,
        'category' => 'Burgers',
        'image' => 'https://images.unsplash.com/photo-1550547990-46340f638856?w=400&h=300&fit=crop'
    ],
    [
        'name' => 'Bacon Burger',
        'description' => 'Crispy bacon with cheddar cheese and grilled onions on a fresh bun',
        'price' => 11.99,
        'category' => 'Burgers',
        'image' => 'https://images.unsplash.com/photo-1553979459-d2229ba7433b?w=400&h=300&fit=crop'
    ],
    [
        'name' => 'Spicy Ramen',
        'description' => 'Hot and flavorful ramen noodles with spiced broth, egg, and vegetable toppings',
        'price' => 8.99,
        'category' => 'Noodles',
        'image' => 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?w=400&h=300&fit=crop'
    ],
    [
        'name' => 'Chicken Ramen',
        'description' => 'Tender chicken pieces with savory broth and fresh ramen noodles',
        'price' => 9.99,
        'category' => 'Noodles',
        'image' => 'https://images.unsplash.com/photo-1584494545473-1f8d645bae89?w=400&h=300&fit=crop'
    ],
    [
        'name' => 'Veggie Ramen',
        'description' => 'Vegetable-based broth with fresh vegetables and premium soft noodles',
        'price' => 8.49,
        'category' => 'Noodles',
        'image' => 'https://images.unsplash.com/photo-1612874742237-6526221fcf4f?w=400&h=300&fit=crop'
    ],
    [
        'name' => 'Falafel Wrap',
        'description' => 'Crispy falafel with hummus, fresh lettuce, tomato, and tahini sauce',
        'price' => 7.99,
        'category' => 'Wraps',
        'image' => 'https://images.unsplash.com/photo-1585238341424-8cfce011e917?w=400&h=300&fit=crop'
    ],
    [
        'name' => 'Grilled Chicken Wrap',
        'description' => 'Marinated grilled chicken with fresh vegetables and creamy garlic mayo',
        'price' => 8.99,
        'category' => 'Wraps',
        'image' => 'https://images.unsplash.com/photo-1585521537430-70becad89107?w=400&h=300&fit=crop'
    ],
    [
        'name' => 'Veggie Wrap',
        'description' => 'Mixed fresh vegetables with hummus and Mediterranean herb sauce',
        'price' => 7.49,
        'category' => 'Wraps',
        'image' => 'https://images.unsplash.com/photo-1599021497298-89c009ec9f9b?w=400&h=300&fit=crop'
    ],
    [
        'name' => 'Chocolate Cake',
        'description' => 'Rich and moist chocolate cake with creamy chocolate frosting and sprinkles',
        'price' => 5.99,
        'category' => 'Desserts',
        'image' => 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=400&h=300&fit=crop'
    ],
    [
        'name' => 'Cheesecake',
        'description' => 'Creamy New York style cheesecake topped with fresh berry compote',
        'price' => 6.99,
        'category' => 'Desserts',
        'image' => 'https://images.unsplash.com/photo-1533134242443-8f2282ba1e91?w=400&h=300&fit=crop'
    ],
    [
        'name' => 'Ice Cream Sundae',
        'description' => 'Vanilla ice cream with hot chocolate sauce, whipped cream, and sprinkles',
        'price' => 4.99,
        'category' => 'Desserts',
        'image' => 'https://images.unsplash.com/photo-1563805042-7684c019e1cb?w=400&h=300&fit=crop'
    ],
    [
        'name' => 'Fresh Lemonade',
        'description' => 'Refreshing homemade lemonade made with fresh lemons and natural honey',
        'price' => 3.99,
        'category' => 'Beverages',
        'image' => 'https://images.unsplash.com/photo-1585518419759-bdf6bd50e10f?w=400&h=300&fit=crop'
    ],
    [
        'name' => 'Iced Coffee',
        'description' => 'Smooth cold brew coffee served over ice with cream and a touch of sugar',
        'price' => 4.49,
        'category' => 'Beverages',
        'image' => 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=400&h=300&fit=crop'
    ],
    [
        'name' => 'Mango Smoothie',
        'description' => 'Creamy mango smoothie blended with yogurt, honey, and fresh fruit',
        'price' => 5.49,
        'category' => 'Beverages',
        'image' => 'https://images.unsplash.com/photo-1553530666-ba2a8e36cd12?w=400&h=300&fit=crop'
    ],
];

$categoryMap = [
    'Pizza' => 1,
    'Burgers' => 2,
    'Noodles' => 3,
    'Wraps' => 4,
    'Desserts' => 5,
    'Beverages' => 6
];

try {
    // Check if items already exist
    $check = $pdo->query("SELECT COUNT(*) FROM menu_items")->fetchColumn();

    if ($check > 0) {
        // Items already exist
        echo "<!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Setup Result</title>
            <style>
                body { font-family: Arial; background: #f5f5f5; padding: 20px; }
                .container { max-width: 600px; margin: 50px auto; }
                .result { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                .info { background: #e3f2fd; border-left: 4px solid #2196f3; padding: 15px; border-radius: 5px; }
                h2 { color: #2196f3; }
                p { color: #666; line-height: 1.6; }
                a { color: #2196f3; text-decoration: none; font-weight: bold; }
                a:hover { text-decoration: underline; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='result'>
                    <h2>✓ Menu Already Populated</h2>
                    <div class='info'>
                        <p>Your food menu database already contains <strong>$check items</strong>. The setup has already been completed!</p>
                        <p style='margin-top: 15px;'><a href='/task4/customer/index.php'>View Your Menu →</a></p>
                    </div>
                </div>
            </div>
        </body>
        </html>";
    } else {
        // Insert menu items
        $inserted = 0;
        $skipped = 0;
        
        foreach ($menuItems as $item) {
            try {
                // Check if item already exists
                $stmt = $pdo->prepare("SELECT id FROM menu_items WHERE name = ?");
                $stmt->execute([$item['name']]);
                
                if ($stmt->fetch()) {
                    $skipped++;
                    continue;
                }
                
                $stmt = $pdo->prepare("INSERT INTO menu_items (name, description, price, category_id, image, is_available) VALUES (?, ?, ?, ?, ?, 1)");
                $stmt->execute([
                    $item['name'],
                    $item['description'],
                    $item['price'],
                    $categoryMap[$item['category']],
                    $item['image']
                ]);
                $inserted++;
            } catch (Exception $e) {
                $skipped++;
            }
        }
        
        echo "<!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Setup Complete</title>
            <style>
                body { font-family: Arial; background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%); padding: 20px; min-height: 100vh; display: flex; align-items: center; }
                .container { max-width: 600px; margin: auto; }
                .result { background: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); text-align: center; }
                .success { background: #4caf50; color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
                h2 { color: #ff6b35; margin: 0 0 10px 0; }
                p { color: #666; line-height: 1.8; }
                .stats { background: #f5f5f5; padding: 20px; border-radius: 8px; margin: 20px 0; }
                .stat-item { font-size: 1.1em; margin: 10px 0; color: #333; }
                .stat-item strong { color: #ff6b35; }
                a { display: inline-block; background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%); color: white; padding: 12px 30px; border-radius: 8px; text-decoration: none; margin: 10px 5px; font-weight: bold; transition: transform 0.3s; }
                a:hover { transform: translateY(-2px); text-decoration: none; color: white; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='result'>
                    <div class='success'>
                        <h2 style='color: white; margin: 0;'>✓ Menu Setup Complete!</h2>
                    </div>
                    <p>Your food menu has been successfully populated with delicious items!</p>
                    <div class='stats'>
                        <div class='stat-item'><strong>$inserted</strong> food items added</div>
                        <div class='stat-item'><strong>6</strong> categories created</div>
                        <div class='stat-item'><strong>High-quality</strong> images from Unsplash</div>
                    </div>
                    <p style='color: #999; font-size: 0.9em;'>Your menu is now ready to serve customers!</p>
                    <div>
                        <a href='/task4/customer/index.php'>🍽️ View Menu</a>
                        <a href='/task4/setup.html'>← Back to Setup</a>
                    </div>
                </div>
            </div>
        </body>
        </html>";
    }
} catch (Exception $e) {
    echo "<!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>Setup Error</title>
        <style>
            body { font-family: Arial; background: #f5f5f5; padding: 20px; }
            .container { max-width: 600px; margin: 50px auto; }
            .error { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-left: 4px solid #f44336; }
            h2 { color: #f44336; }
            p { color: #666; }
            .error-details { background: #ffebee; padding: 15px; border-radius: 5px; margin-top: 15px; font-family: monospace; font-size: 0.9em; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='error'>
                <h2>✗ Error During Setup</h2>
                <p>An error occurred while populating the menu:</p>
                <div class='error-details'>" . htmlspecialchars($e->getMessage()) . "</div>
                <p style='margin-top: 15px;'><a href='/task4/setup.html'>← Try Again</a></p>
            </div>
        </div>
    </body>
    </html>";
}
?>
