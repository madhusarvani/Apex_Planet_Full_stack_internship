<?php
require_once '../config/db.php';
require_once '../inc/auth.php';
require_once '../inc/functions.php';

if (!isAdmin()) redirect('../dashboard.php');

// Delete user logic
if (isset($_GET['delete_id']) && isset($_GET['confirm']) && $_GET['confirm'] == 'yes') {
    $delete_id = (int)$_GET['delete_id'];
    if ($delete_id != $_SESSION['user_id']) {
        $stmt = $pdo->prepare("SELECT profile_picture FROM users WHERE id = ?");
        $stmt->execute([$delete_id]);
        $user_pic = $stmt->fetch();
        if ($user_pic && $user_pic['profile_picture'] != 'default.png' && file_exists("../uploads/" . $user_pic['profile_picture'])) {
            unlink("../uploads/" . $user_pic['profile_picture']);
        }
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$delete_id]);
        $success = "User deleted successfully!";
    } else {
        $error = "You cannot delete your own account!";
    }
}

$stmt = $pdo->prepare("SELECT u.*, r.role_name FROM users u JOIN roles r ON u.role_id = r.id ORDER BY u.created_at DESC");
$stmt->execute();
$users = $stmt->fetchAll();

include '../inc/header.php';
?>

<!-- Same card style as edit_profile.php -->
<div class="card">
    <div class="card-header">
        <h4><i class="fas fa-users me-2"></i>Manage Users</h4>
    </div>
    <div class="card-body">
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="mb-3">
            <a href="add_user.php" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Add New User</a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Profile</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Registered</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><img src="<?= getProfileImageUrl($user['profile_picture']) ?>" style="width:40px;height:40px;border-radius:50%;object-fit:cover;"></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><span class="badge bg-<?= $user['role_name'] == 'Admin' ? 'danger' : 'info' ?>"><?= $user['role_name'] ?></span></td>
                        <td><?= date('d M Y', strtotime($user['created_at'])) ?></td>
                        <td>
                            <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="javascript:void(0)" onclick="confirmDelete(<?= $user['id'] ?>, '<?= htmlspecialchars($user['username']) ?>')" class="btn btn-sm btn-danger">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, name) {
    if (confirm('Delete user "' + name + '"? This cannot be undone.')) {
        window.location.href = 'users.php?delete_id=' + id + '&confirm=yes';
    }
}
</script>

<?php include '../inc/footer.php'; ?>