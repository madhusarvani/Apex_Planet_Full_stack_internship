<?php
require_once '../config/db.php';
require_once '../inc/auth.php';
if(!isAdmin()) redirect(BASE_URL.'/customer/index.php');
$users = $pdo->query("SELECT id, name, email, created_at FROM users WHERE role='customer' ORDER BY created_at DESC")->fetchAll();
include '../inc/header.php';
?>
<div class="card">
    <div class="card-header">Registered Customers</div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Registered Date</th></tr></thead>
            <tbody>
            <?php foreach($users as $user): ?>
            <tr>
                <td><?= $user['id'] ?></td>
                <td><?= htmlspecialchars($user['name']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= date('d M Y', strtotime($user['created_at'])) ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include '../inc/footer.php'; ?>