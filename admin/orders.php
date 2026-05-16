<?php
require_once '../config/db.php';
require_once '../inc/auth.php';
if (!isAdmin()) redirect('../customer/index.php');

// Update order status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = (int)$_POST['order_id'];
    $status = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$status, $order_id]);
    header("Location: orders.php?msg=updated");
    exit;
}

$orders = $pdo->query("SELECT o.*, u.name as customer_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.order_date DESC")->fetchAll();
include '../inc/header.php';
?>
<div class="card">
    <div class="card-header"><h4>Manage Orders</h4></div>
    <div class="card-body">
        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success">Order status updated.</div>
        <?php endif; ?>
        <table class="table table-bordered">
            <thead><tr><th>Order ID</th><th>Customer</th><th>Date</th><th>Total</th><th>Payment</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
                <?php foreach($orders as $order): ?>
                <tr>
                    <td>#<?= $order['id'] ?></td>
                    <td><?= htmlspecialchars($order['customer_name']) ?></td>
                    <td><?= date('d M Y, h:i A', strtotime($order['order_date'])) ?></td>
                    <td>$<?= number_format($order['total_amount'],2) ?></td>
                    <td><?= $order['payment_method'] ?? 'COD' ?></td>
                    <td>
                        <span class="badge bg-<?php
                            if($order['status']=='pending') echo 'warning';
                            elseif($order['status']=='preparing') echo 'info';
                            elseif($order['status']=='out_for_delivery') echo 'primary';
                            elseif($order['status']=='delivered') echo 'success';
                            else echo 'secondary';
                        ?>"><?= ucfirst(str_replace('_',' ',$order['status'])) ?></span>
                    </td>
                    <td>
                        <form method="POST" style="display:inline-flex; gap:5px;">
                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                            <select name="status" class="form-select form-select-sm" style="width:140px;">
                                <option value="pending" <?= $order['status']=='pending'?'selected':'' ?>>Pending</option>
                                <option value="preparing" <?= $order['status']=='preparing'?'selected':'' ?>>Preparing</option>
                                <option value="out_for_delivery" <?= $order['status']=='out_for_delivery'?'selected':'' ?>>Out for Delivery</option>
                                <option value="delivered" <?= $order['status']=='delivered'?'selected':'' ?>>Delivered</option>
                                <option value="cancelled" <?= $order['status']=='cancelled'?'selected':'' ?>>Cancelled</option>
                            </select>
                            <button type="submit" class="btn btn-sm btn-primary">Update</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include '../inc/footer.php'; ?>