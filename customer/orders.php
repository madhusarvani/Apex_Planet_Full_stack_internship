<?php
require_once '../config/db.php';
require_once '../inc/auth.php';

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();

// Show success message if redirected from checkout
$show_success = isset($_GET['success']) && $_GET['success'] == 1;

include '../inc/header.php';
?>

<?php if ($show_success): ?>
    <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
        <i class="fas fa-check-circle fa-2x mb-2 d-block"></i>
        <strong>Order placed successfully!</strong> Your food is on its way. 🍕
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">My Orders</h5>
    </div>
    <div class="card-body p-0">
        <?php if (empty($orders)): ?>
            <div class="text-center py-5">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <p>You haven't placed any orders yet.</p>
                <a href="index.php" class="btn btn-primary">Start Ordering</a>
            </div>
        <?php else: ?>
            <div class="orders-container">
                <?php 
                $order_number = count($orders);
                foreach ($orders as $order): 
                ?>
                    <div class="card mb-4 order-card">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1"><strong>Order #<?= $order_number ?></strong></h6>
                                <small class="text-muted"><?= date('d M Y, h:i A', strtotime($order['order_date'])) ?></small>
                            </div>
                            <div class="text-end">
                                <div class="mb-2">
                                    <?php
                                    $badge_class = 'secondary';
                                    if ($order['status'] == 'pending') $badge_class = 'warning';
                                    elseif ($order['status'] == 'preparing') $badge_class = 'info';
                                    elseif ($order['status'] == 'out_for_delivery') $badge_class = 'primary';
                                    elseif ($order['status'] == 'delivered') $badge_class = 'success';
                                    elseif ($order['status'] == 'cancelled') $badge_class = 'danger';
                                    ?>
                                    <span class="badge bg-<?= $badge_class ?>"><?= ucfirst(str_replace('_', ' ', $order['status'])) ?></span>
                                </div>
                                <h5 class="mb-0">$<?= number_format($order['total_amount'], 2) ?></h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php
                            // Get order items with menu details
                            $stmt = $pdo->prepare("SELECT oi.*, mi.name, mi.image FROM order_items oi JOIN menu_items mi ON oi.menu_item_id = mi.id WHERE oi.order_id = ?");
                            $stmt->execute([$order['id']]);
                            $order_items = $stmt->fetchAll();
                            ?>
                            <div class="row g-3">
                                <?php foreach ($order_items as $item): 
                                    // Construct proper image URL
                                    $img_url = (strpos($item['image'], 'http') === 0) 
                                        ? $item['image']
                                        : BASE_URL . '/assets/uploads/' . $item['image'];
                                ?>
                                    <div class="col-md-6 col-lg-4">
                                        <div class="order-item-card d-flex flex-column">
                                            <img src="<?= $img_url ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="order-item-img mb-2" style="width: 100%; height: 150px; object-fit: cover; border-radius: 8px;">
                                            <h6 class="mb-1"><?= htmlspecialchars($item['name']) ?></h6>
                                            <small class="text-muted mb-2">Qty: <strong><?= $item['quantity'] ?></strong> × $<?= number_format($item['price'], 2) ?></small>
                                            <small class="text-primary fw-bold">$<?= number_format($item['quantity'] * $item['price'], 2) ?></small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php
                    $order_number--;
                    endforeach; 
                    ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.table th, .table td {
    vertical-align: middle;
}
.badge {
    font-size: 0.75rem;
    padding: 0.4rem 0.8rem;
    border-radius: 30px;
}
.alert {
    border-radius: 20px;
    background: #d4edda;
    color: #155724;
    border: none;
}
.order-card {
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    transition: all 0.3s ease;
}
.order-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
.order-item-card {
    padding: 12px;
    background: #f9f9f9;
    border-radius: 8px;
    transition: all 0.3s ease;
}
.order-item-card:hover {
    background: #f0f0f0;
    transform: translateY(-2px);
}
.order-item-img {
    border-radius: 8px;
    object-fit: cover;
}
.orders-container {
    padding: 1rem 0;
}
</style>

<?php include '../inc/footer.php'; ?>