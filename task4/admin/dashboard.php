<?php
require_once '../config/db.php';
require_once '../inc/auth.php';
if (!isAdmin()) redirect('../customer/index.php');

// Stats
$total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$total_revenue = $pdo->query("SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE status = 'delivered'")->fetchColumn();
$pending_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
$total_items = $pdo->query("SELECT COUNT(*) FROM menu_items")->fetchColumn();

// Orders per day (last 7 days)
$stmt = $pdo->prepare("
    SELECT DATE(order_date) as date, COUNT(*) as count
    FROM orders
    WHERE order_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    GROUP BY DATE(order_date)
    ORDER BY date ASC
");
$stmt->execute();
$orders_per_day = $stmt->fetchAll();
$labels = [];
$data = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $labels[] = date('d M', strtotime($date));
    $found = false;
    foreach ($orders_per_day as $row) {
        if ($row['date'] == $date) {
            $data[] = $row['count'];
            $found = true;
            break;
        }
    }
    if (!$found) $data[] = 0;
}

// Top selling items (by quantity)
$top_items = $pdo->query("
    SELECT m.name, SUM(oi.quantity) as total_sold
    FROM order_items oi
    JOIN menu_items m ON oi.menu_item_id = m.id
    GROUP BY oi.menu_item_id
    ORDER BY total_sold DESC
    LIMIT 5
")->fetchAll();

// Recent orders
$recent_orders = $pdo->query("
    SELECT o.id, u.name as customer, o.total_amount, o.status, o.order_date
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.order_date DESC
    LIMIT 5
")->fetchAll();

include '../inc/header.php';
?>

<div class="row g-4 mb-4">
    <div class="col-md-3"><div class="card text-center p-3"><h3><?= $total_orders ?></h3><p>Total Orders</p></div></div>
    <div class="col-md-3"><div class="card text-center p-3"><h3>$<?= number_format($total_revenue,2) ?></h3><p>Revenue (Delivered)</p></div></div>
    <div class="col-md-3"><div class="card text-center p-3"><h3><?= $pending_orders ?></h3><p>Pending Orders</p></div></div>
    <div class="col-md-3"><div class="card text-center p-3"><h3><?= $total_items ?></h3><p>Menu Items</p></div></div>
</div>

<div class="row g-4">
    <!-- Orders per day chart -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Orders Per Day (Last 7 Days)</div>
            <div class="card-body">
                <canvas id="ordersChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Top selling items -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">Top Selling Items</div>
            <div class="card-body">
                <?php if (empty($top_items)): ?>
                    <p class="text-muted">No sales data yet.</p>
                <?php else: ?>
                    <ul class="list-group">
                        <?php foreach ($top_items as $item): ?>
                            <li class="list-group-item d-flex justify-content-between">
                                <span><?= htmlspecialchars($item['name']) ?></span>
                                <span class="badge bg-primary rounded-pill"><?= $item['total_sold'] ?> sold</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">Recent Orders</div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th>Order ID</th><th>Customer</th><th>Date</th><th>Total</th><th>Status</th><th>Action</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_orders as $order): ?>
                        <tr>
                            <td>#<?= $order['id'] ?></td>
                            <td><?= htmlspecialchars($order['customer']) ?></td>
                            <td><?= date('d M Y, h:i A', strtotime($order['order_date'])) ?></td>
                            <td>$<?= number_format($order['total_amount'],2) ?></td>
                            <td><span class="badge bg-<?= $order['status']=='pending'?'warning':($order['status']=='delivered'?'success':'secondary') ?>"><?= ucfirst($order['status']) ?></span></td>
                            <td><a href="orders.php" class="btn btn-sm btn-primary">View</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('ordersChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
            label: 'Orders',
            data: <?= json_encode($data) ?>,
            backgroundColor: '#ff6b35',
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } }
    }
});
</script>

<?php include '../inc/footer.php'; ?>