<?php
require_once 'config/db.php';
require_once 'inc/auth.php';
require_once 'inc/functions.php';

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT u.*, r.role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
include 'inc/header.php';
?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">My Profile</div>
            <div class="card-body text-center">
                <img src="<?= getProfileImageUrl($user['profile_picture']) ?>" class="rounded-circle mb-3" style="width:150px;height:150px;object-fit:cover;">
                <table class="table table-bordered mt-3">
                    <tr><th>Username</th><td><?= htmlspecialchars($user['username']) ?></td></tr>
                    <tr><th>Email</th><td><?= htmlspecialchars($user['email']) ?></td></tr>
                    <tr><th>Role</th><td><?= htmlspecialchars($user['role_name']) ?></td></tr>
                    <tr><th>Member Since</th><td><?= date('F j, Y', strtotime($user['created_at'])) ?></td></tr>
                </table>
                <a href="edit_profile.php" class="btn btn-primary">Edit Profile</a>
                <a href="dashboard.php" class="btn btn-secondary">Back</a>
            </div>
        </div>
    </div>
</div>
<?php include 'inc/footer.php'; ?>