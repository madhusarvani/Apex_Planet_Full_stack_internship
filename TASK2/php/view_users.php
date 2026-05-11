<?php
$auth_password = "admin@123";
if (!isset($_SERVER['PHP_AUTH_PW']) || $_SERVER['PHP_AUTH_PW'] !== $auth_password) {
    header('WWW-Authenticate: Basic realm="Admin Only"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Access denied';
    exit;
}
// Optional: Add a simple password protection for this page
// $auth_password = "admin123"; // uncomment to protect
// if (!isset($_SERVER['PHP_AUTH_PW']) || $_SERVER['PHP_AUTH_PW'] !== $auth_password) {
//     header('WWW-Authenticate: Basic realm="Admin Only"');
//     header('HTTP/1.0 401 Unauthorized');
//     echo 'Access denied';
//     exit;
// }

require_once 'config.php';

$result = $conn->query("SELECT id, username, email, created_at FROM users ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registered Users - Admin View</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">📋 Registered Users (<?php echo $result->num_rows; ?>)</h4>
            </div>
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th><th>Username</th><th>Email</th><th>Registered On</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo $row['created_at']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if($result->num_rows === 0): ?>
                        <tr><td colspan="4" class="text-center">No users registered yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <a href="../index.html" class="btn btn-secondary">Back to Home</a>
            </div>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>