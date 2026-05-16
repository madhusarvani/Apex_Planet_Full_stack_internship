<?php
require_once '../config/db.php';
require_once '../inc/auth.php';

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    redirect(BASE_URL . '/customer/cart.php');
}

$cart_items = [];
$subtotal = 0;

$ids = array_keys($cart);
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$stmt = $pdo->prepare("SELECT * FROM menu_items WHERE id IN ($placeholders)");
$stmt->execute($ids);
$items = $stmt->fetchAll();

foreach ($items as $item) {
    $qty = $cart[$item['id']]['quantity'];
    $item_total = $item['price'] * $qty;
    $subtotal += $item_total;
    $cart_items[] = [
        'id' => $item['id'],
        'name' => $item['name'],
        'price' => $item['price'],
        'quantity' => $qty,
        'item_total' => $item_total
    ];
}

// Fee calculations
$delivery_fee = ($subtotal > 30) ? 0 : 4.99;
$packaging_fee = 1.50;
$tax_rate = 0.08;
$tax = $subtotal * $tax_rate;
$platform_fee = 1.99;
$total = $subtotal + $delivery_fee + $packaging_fee + $tax + $platform_fee;

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = trim($_POST['address']);
    $payment_method = $_POST['payment_method'];

    if (empty($address)) {
        $error = "Delivery address is required.";
    } else {
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, delivery_address, payment_method, status) VALUES (?, ?, ?, ?, 'pending')");
            $stmt->execute([$_SESSION['user_id'], $total, $address, $payment_method]);
            $order_id = $pdo->lastInsertId();

            foreach ($cart_items as $item) {
                $stmt = $pdo->prepare("INSERT INTO order_items (order_id, menu_item_id, quantity, price) VALUES (?, ?, ?, ?)");
                $stmt->execute([$order_id, $item['id'], $item['quantity'], $item['price']]);
            }
            $pdo->commit();
            unset($_SESSION['cart']);
            redirect(BASE_URL . '/customer/orders.php?success=1');
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Order failed: " . $e->getMessage();
        }
    }
}

include '../inc/header.php';
?>

<div class="row g-4">
    <!-- Left: Order Summary -->
    <div class="col-lg-7">
        <div class="card checkout-card">
            <div class="card-header">
                <h5 class="mb-0">Order Summary</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-borderless mb-0">
                        <thead class="table-light">
                            <tr><th>Item</th><th>Qty</th><th>Price</th><th>Total</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart_items as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['name']) ?></td>
                                <td><?= $item['quantity'] ?></td>
                                <td>$<?= number_format($item['price'], 2) ?></td>
                                <td>$<?= number_format($item['item_total'], 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="p-3 bg-light">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Subtotal</span><span>$<?= number_format($subtotal, 2) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span>Delivery Fee</span><span><?= $delivery_fee > 0 ? '$'.number_format($delivery_fee,2) : 'FREE' ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span>Packaging Fee</span><span>$<?= number_format($packaging_fee, 2) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span>Tax (8%)</span><span>$<?= number_format($tax, 2) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Platform Fee</span><span>$<?= number_format($platform_fee, 2) ?></span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold fs-5">
                        <span>Total</span><span>$<?= number_format($total, 2) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right: Checkout Form with Payment Options -->
    <div class="col-lg-5">
        <div class="card checkout-card">
            <div class="card-header">
                <h5 class="mb-0">Delivery & Payment</h5>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Delivery Address</label>
                        <textarea name="address" class="form-control" rows="3" required placeholder="House/Flat No., Street, City, PIN"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Payment Method</label>
                        <div class="payment-options">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" id="cod" value="Cash on Delivery" checked>
                                <label class="form-check-label" for="cod">
                                    <i class="fas fa-money-bill-wave me-2"></i> Cash on Delivery
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" id="card" value="Card">
                                <label class="form-check-label" for="card">
                                    <i class="fas fa-credit-card me-2"></i> Credit / Debit Card
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" id="upi" value="UPI">
                                <label class="form-check-label" for="upi">
                                    <i class="fas fa-mobile-alt me-2"></i> UPI (Google Pay, PhonePe, etc.)
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" id="netbanking" value="Net Banking">
                                <label class="form-check-label" for="netbanking">
                                    <i class="fas fa-university me-2"></i> Net Banking
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" id="wallet" value="Wallet">
                                <label class="form-check-label" for="wallet">
                                    <i class="fas fa-wallet me-2"></i> Foodie Wallet
                                </label>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2">Place Order</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.checkout-card {
    border: none;
    border-radius: 20px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.05);
    margin-bottom: 1.5rem;
}
.payment-options {
    background: #f9fafc;
    padding: 1rem;
    border-radius: 16px;
}
.form-check-input:checked {
    background-color: var(--primary, #ff6b35);
    border-color: var(--primary, #ff6b35);
}
</style>

<?php include '../inc/footer.php'; ?>