<?php
require_once 'config/db.php';

$email = 'admin@foodie.com';
$new_password = 'admin123';

$hashed = password_hash($new_password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
$stmt->execute([$hashed, $email]);

echo "Password for $email has been reset to: $new_password<br>";
echo "New hash: $hashed<br>";
echo "<a href='login.php'>Go to Login</a>";
?>