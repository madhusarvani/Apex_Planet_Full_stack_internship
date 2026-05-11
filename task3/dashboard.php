<?php
require_once 'config/db.php';
require_once 'inc/auth.php';
require_once 'inc/functions.php';
include 'inc/header.php';
?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white">Dashboard</div>
            <div class="card-body">
                <h4>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</h4>
                <p>Role: <strong><?= htmlspecialchars($_SESSION['role']) ?></strong></p>
                <?php if (isAdmin()): ?>
                    <div class="alert alert-info">
                        <h5>Admin Panel</h5>
                        <a href="admin/users.php" class="btn btn-primary">Manage Users</a>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <a href="profile.php" class="btn btn-primary">View Profile</a>
                        <a href="edit_profile.php" class="btn btn-secondary">Edit Profile</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php include 'inc/footer.php'; ?>