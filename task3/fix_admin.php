<?php
require_once 'config/db.php';

// Set new credentials
$new_username = 'admin';
$new_email = 'admin@example.com';
$new_password = 'admin123'; // Change this to whatever password you want

// Generate proper hash
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Update the admin user (id = 1)
$stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = 1");
$result = $stmt->execute([$new_username, $new_email, $hashed_password]);

if ($result) {
    echo "✅ Admin credentials updated successfully!<br>";
    echo "Username: <strong>$new_username</strong><br>";
    echo "Password: <strong>$new_password</strong><br>";
    echo "<a href='login.php'>Go to Login</a>";
} else {
    echo "❌ Update failed. Check your database connection.";
}
?>