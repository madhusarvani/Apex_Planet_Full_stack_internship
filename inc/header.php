<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoodieDash - Order Food Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="<?= BASE_URL ?>/customer/index.php">
            <i class="fas fa-utensils me-2"></i>FoodieDash
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php if (!isLoggedIn()): ?>
                    <!-- Not logged in -->
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/login.php">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/register.php">Register</a></li>
                <?php elseif (isAdmin()): ?>
                    <!-- Admin logged in -->
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/admin/dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/admin/menu.php">Manage Menu</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/admin/orders.php">Manage Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/customer/profile.php">Profile</a></li>
                    <li class="nav-item"><a class="nav-link text-danger" href="<?= BASE_URL ?>/logout.php">Logout</a></li>
                <?php else: ?>
                    <!-- Regular customer logged in -->
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/customer/index.php">Menu</a></li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>/customer/cart.php">
                            <i class="fas fa-shopping-cart"></i> Cart
                            <span id="cart-count" class="cart-badge"><?= getCartCount() ?></span>
                        </a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/customer/orders.php">My Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/customer/profile.php">Profile</a></li>
                    <li class="nav-item"><a class="nav-link text-danger" href="<?= BASE_URL ?>/logout.php">Logout</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<main>
    <?php if (!isset($no_container) || !$no_container): ?>
    <div class="container">
    <?php endif; ?>