<?php
require_once 'config/db.php';
require_once 'inc/auth.php';
require_once 'inc/functions.php';

$user_id = $_SESSION['user_id'];
$error = $success = '';
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $profile_picture = uploadProfilePicture($_FILES['profile_picture'], $user['profile_picture']);
    if ($profile_picture === false && $_FILES['profile_picture']['error'] !== UPLOAD_ERR_NO_FILE) {
        $error = "Invalid file. Only JPG, PNG, GIF under 2MB.";
    } else {
        if (!empty($new_password)) {
            if (!password_verify($current_password, $user['password'])) {
                $error = "Current password is incorrect";
            } elseif ($new_password !== $confirm_password) {
                $error = "New passwords do not match";
            } elseif (strlen($new_password) < 6) {
                $error = "New password must be at least 6 characters";
            }
        }
        if (empty($error)) {
            $updateFields = "username = ?, email = ?, profile_picture = ?";
            $params = [$username, $email, $profile_picture];
            if (!empty($new_password)) {
                $updateFields .= ", password = ?";
                $params[] = password_hash($new_password, PASSWORD_DEFAULT);
            }
            $sql = "UPDATE users SET $updateFields WHERE id = ?";
            $params[] = $user_id;
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $_SESSION['username'] = $username;
            $success = "Profile updated successfully!";
            // Refresh user data
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();
        }
    }
}
include 'inc/header.php';
?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">Edit Profile</div>
            <div class="card-body">
                <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
                <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
                <form method="POST" enctype="multipart/form-data">
                    <div class="text-center mb-3">
                        <img id="preview" src="<?= getProfileImageUrl($user['profile_picture']) ?>" class="rounded-circle mb-2" style="width:100px;height:100px;object-fit:cover;">
                        <br><label class="btn btn-secondary btn-sm">Change Picture<input type="file" name="profile_picture" accept="image/*" onchange="previewImage(this)" style="display:none;"></label>
                    </div>
                    <div class="mb-3"><label>Username</label><input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required></div>
                    <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required></div>
                    <hr><h5>Change Password (Optional)</h5>
                    <div class="mb-3"><label>Current Password</label><input type="password" name="current_password" class="form-control"></div>
                    <div class="mb-3"><label>New Password</label><input type="password" name="new_password" class="form-control"></div>
                    <div class="mb-3"><label>Confirm New Password</label><input type="password" name="confirm_password" class="form-control"></div>
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                    <a href="profile.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) { document.getElementById('preview').src = e.target.result; };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
<?php include 'inc/footer.php'; ?>