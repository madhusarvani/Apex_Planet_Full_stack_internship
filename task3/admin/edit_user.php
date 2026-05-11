<?php
require_once '../config/db.php';
require_once '../inc/auth.php';
require_once '../inc/functions.php';

if (!isAdmin()) {
    redirect('../dashboard.php');
}

$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$error = '';

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    redirect('users.php?error=User+not+found');
}

$roles = $pdo->query("SELECT * FROM roles")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role_id = $_POST['role_id'];
    $new_password = $_POST['new_password'];

    // Handle profile picture upload
    $profile_picture = uploadProfilePicture($_FILES['profile_picture'], $user['profile_picture']);
    if ($profile_picture === false && $_FILES['profile_picture']['error'] !== UPLOAD_ERR_NO_FILE) {
        $error = "Invalid image. Only JPG, PNG, GIF under 2MB.";
    } else {
        $updateFields = "username = ?, email = ?, role_id = ?, profile_picture = ?";
        $params = [$username, $email, $role_id, $profile_picture];
        
        if (!empty($new_password)) {
            if (strlen($new_password) < 6) {
                $error = "Password must be at least 6 characters.";
            } else {
                $updateFields .= ", password = ?";
                $params[] = password_hash($new_password, PASSWORD_DEFAULT);
            }
        }
        
        if (empty($error)) {
            $sql = "UPDATE users SET $updateFields WHERE id = ?";
            $params[] = $user_id;
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            
            // ✅ Redirect to Manage Users page after successful update
            redirect('users.php?success=User+updated+successfully');
        }
    }
}

include '../inc/header.php';
?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-edit me-2"></i>Edit User: <?= htmlspecialchars($user['username']) ?></h4>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="text-center mb-3">
                        <img id="preview" src="<?= getProfileImageUrl($user['profile_picture']) ?>" 
                             class="profile-img mb-2" style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%;">
                        <br>
                        <label class="btn btn-secondary btn-sm">
                            <i class="fas fa-upload"></i> Change Picture
                            <input type="file" name="profile_picture" accept="image/*" onchange="previewImage(this)" style="display: none;">
                        </label>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role_id" class="form-select" required>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= $role['id'] ?>" <?= ($role['id'] == $user['role_id']) ? 'selected' : '' ?>>
                                    <?= $role['role_name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">New Password (Optional)</label>
                        <input type="password" name="new_password" class="form-control" placeholder="Leave blank to keep current password">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Update User</button>
                    <a href="users.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
<?php include '../inc/footer.php'; ?>