<?php
require_once 'config/db.php';
require_once 'inc/functions.php';
if(isLoggedIn()) redirect(BASE_URL.'/customer/index.php');
$error = '';
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    if(empty($name) || empty($email) || empty($password)) $error = "All fields required";
    elseif($password !== $confirm) $error = "Passwords do not match";
    elseif(strlen($password) < 6) $error = "Password min 6 characters";
    else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if($stmt->fetch()) $error = "Email already registered";
        else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?,?,?,'customer')");
            $stmt->execute([$name, $email, $hashed]);
            redirect(BASE_URL.'/login.php');
        }
    }
}
include 'inc/header.php';
?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Register</div>
            <div class="card-body">
                <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
                <form method="POST">
                    <div class="mb-3"><label>Full Name</label><input type="text" name="name" class="form-control" required></div>
                    <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" required></div>
                    <div class="mb-3"><label>Password</label><input type="password" name="password" class="form-control" required></div>
                    <div class="mb-3"><label>Confirm Password</label><input type="password" name="confirm_password" class="form-control" required></div>
                    <button type="submit" class="btn btn-primary">Register</button>
                    <a href="login.php" class="btn btn-link">Already have an account?</a>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include 'inc/footer.php'; ?>