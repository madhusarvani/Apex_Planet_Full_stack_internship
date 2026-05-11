<?php
require_once 'config/db.php';
require_once 'inc/functions.php';

if (isLoggedIn()) redirect('dashboard.php');

$error = '';
$body_class = 'login-page';
$no_container = true; // remove inner container
$main_class = ''; // no extra padding

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Please enter username/email and password";
    } else {
        $stmt = $pdo->prepare("SELECT u.*, r.role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.username = ? OR u.email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role_name'];
            redirect('dashboard.php');
        } else {
            $error = "Invalid credentials";
        }
    }
}
include 'inc/header.php';
?>
<div class="auth-card card">
    <div class="card-header text-center">
        <h4 class="mb-1"><i class="fas fa-lock me-2"></i>Sign In</h4>
        <p class="text-muted small">Welcome back</p>
    </div>
    <div class="card-body">
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" id="loginForm">
            <div class="mb-3">
                <label class="form-label">Email or Username</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" name="username" class="form-control" required>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                    <input type="password" name="password" class="form-control" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 btn-animate" id="loginBtn">
                <span class="btn-text">Login <i class="fas fa-arrow-right ms-1"></i></span>
                <span class="spinner" style="display:none;"></span>
            </button>
        </form>
        <hr>
        <p class="text-center mb-0">Don't have an account? <a href="register.php">Register</a></p>
    </div>
</div>
<script>
document.getElementById('loginForm').addEventListener('submit', function() {
    const btn = document.getElementById('loginBtn');
    btn.classList.add('btn-loading');
    btn.querySelector('.btn-text').style.display = 'none';
    btn.querySelector('.spinner').style.display = 'inline-block';
});
<?php if ($error): ?>
    document.querySelector('.alert-danger')?.classList.add('shake');
<?php endif; ?>
</script>
<?php include 'inc/footer.php'; ?>