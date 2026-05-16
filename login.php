<?php
require_once 'config/db.php';
require_once 'inc/functions.php';
if(isLoggedIn()) redirect(BASE_URL.'/customer/index.php');
$error = '';
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
        redirect(BASE_URL . ($user['role'] == 'admin' ? '/admin/dashboard.php' : '/customer/index.php'));
    } else {
        $error = "Invalid email or password";
    }
}
include 'inc/header.php';
?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Login</div>
            <div class="card-body">
                <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
                <form method="POST">
                    <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" required></div>
                    <div class="mb-3"><label>Password</label><input type="password" name="password" class="form-control" required></div>
                    <button type="submit" class="btn btn-primary">Login</button>
                    <a href="forgot_password.php" class="btn btn-link">Forgot Password?</a>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include 'inc/footer.php'; ?>