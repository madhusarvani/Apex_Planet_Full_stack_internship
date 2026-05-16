<?php
require_once '../config/db.php';
require_once '../inc/functions.php';

if (!isLoggedIn()) {
    redirect(BASE_URL . '/login.php');
}

$cart = $_SESSION['cart'] ?? [];
$cart_items = [];
$subtotal = 0;

if (!empty($cart)) {
    $ids = array_keys($cart);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $items = $stmt->fetchAll();
    
    // Remove items from cart that no longer exist in menu
    $existing_ids = array_column($items, 'id');
    foreach ($ids as $id) {
        if (!in_array($id, $existing_ids)) {
            unset($_SESSION['cart'][$id]);
        }
    }
    
    foreach ($items as $item) {
        $qty = $cart[$item['id']]['quantity'];
        $item_total = $item['price'] * $qty;
        $subtotal += $item_total;
        $cart_items[] = [
            'id' => $item['id'],
            'name' => $item['name'],
            'price' => $item['price'],
            'quantity' => $qty,
            'image' => $item['image'],
            'item_total' => $item_total
        ];
    }
}

// Fee calculations (only apply if cart has items)
if (!empty($cart_items)) {
    $delivery_fee = ($subtotal > 30) ? 0 : 4.99;   // free delivery over $30
    $packaging_fee = 1.50;                         // fixed per order
    $tax_rate = 0.08;                              // 8% tax
    $tax = $subtotal * $tax_rate;
    $platform_fee = 1.99;
} else {
    $delivery_fee = 0;
    $packaging_fee = 0;
    $tax_rate = 0.08;
    $tax = 0;
    $platform_fee = 0;
}
$total = $subtotal + $delivery_fee + $packaging_fee + $tax + $platform_fee;

// Handle quantity update via AJAX (or form)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $item_id = (int)$_POST['item_id'];
    $action = $_POST['action'];
    if ($action === 'increment') {
        $_SESSION['cart'][$item_id]['quantity']++;
    } elseif ($action === 'decrement') {
        $_SESSION['cart'][$item_id]['quantity']--;
        if ($_SESSION['cart'][$item_id]['quantity'] <= 0) {
            unset($_SESSION['cart'][$item_id]);
        }
    }
    header("Location: cart.php");
    exit;
}

include '../inc/header.php';
?>

<div class="cart-container">
    <div class="row g-4">
        <!-- Left column: Cart items -->
        <div class="col-lg-7">
            <div class="card cart-card">
                <div class="card-header">
                    <h5 class="mb-0">Your Cart</h5>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($cart_items)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                            <p>Your cart is empty.</p>
                            <a href="index.php" class="btn btn-primary">Browse Menu</a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($cart_items as $item): ?>
                            <div class="cart-item d-flex align-items-center p-3 border-bottom">
                                <img src="<?= $item['image'] ?>" class="cart-item-img rounded" alt="<?= $item['name'] ?>">
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1"><?= htmlspecialchars($item['name']) ?></h6>
                                    <span class="text-muted small">$<?= number_format($item['price'], 2) ?></span>
                                </div>
                                <div class="quantity-control d-flex align-items-center me-3">
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                        <input type="hidden" name="action" value="decrement">
                                        <button type="submit" class="btn btn-outline-secondary btn-sm">-</button>
                                    </form>
                                    <span class="mx-2 fw-bold"><?= $item['quantity'] ?></span>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                        <input type="hidden" name="action" value="increment">
                                        <button type="submit" class="btn btn-outline-secondary btn-sm">+</button>
                                    </form>
                                </div>
                                <div class="item-total fw-bold">
                                    $<?= number_format($item['item_total'], 2) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right column: Order summary with fee breakdown -->
        <div class="col-lg-5">
            <div class="card summary-card">
                <div class="card-header">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <div class="summary-row d-flex justify-content-between mb-2">
                        <span>Item total</span>
                        <span>$<?= number_format($subtotal, 2) ?></span>
                    </div>
                    <div class="summary-row d-flex justify-content-between mb-2 text-muted">
                        <span>Delivery fee</span>
                        <span>
                            <?php if (!empty($cart_items) && $delivery_fee == 0): ?>
                                <span class="text-success">FREE</span>
                            <?php else: ?>
                                $<?= number_format($delivery_fee, 2) ?>
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="summary-row d-flex justify-content-between mb-2 text-muted">
                        <span>Packaging fee</span>
                        <span>$<?= number_format($packaging_fee, 2) ?></span>
                    </div>
                    <div class="summary-row d-flex justify-content-between mb-2 text-muted">
                        <span>Tax (<?= $tax_rate * 100 ?>%)</span>
                        <span>$<?= number_format($tax, 2) ?></span>
                    </div>
                    <div class="summary-row d-flex justify-content-between mb-3 text-muted">
                        <span>Platform fee</span>
                        <span>$<?= number_format($platform_fee, 2) ?></span>
                    </div>
                    <hr>
                    <div class="summary-row d-flex justify-content-between fw-bold fs-5">
                        <span>To Pay:</span>
                        <span>$<?= number_format($total, 2) ?></span>
                    </div>
                    <a href="checkout.php" class="btn btn-primary w-100 mt-4 py-2 <?= empty($cart_items) ? 'disabled' : '' ?>">
                        Proceed to Checkout
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.cart-card, .summary-card {
    border: none;
    border-radius: 24px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.05);
    overflow: hidden;
}
.cart-item-img {
    width: 70px;
    height: 70px;
    object-fit: cover;
}
.quantity-control .btn {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.summary-row {
    font-size: 0.95rem;
}
@media (max-width: 768px) {
    .cart-item-img { width: 50px; height: 50px; }
    .cart-item { flex-wrap: wrap; }
}
</style>

<?php include '../inc/footer.php'; ?>