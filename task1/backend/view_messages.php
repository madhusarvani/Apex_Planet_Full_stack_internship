<?php
include 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Messages | Admin</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2rem; background: #f4f6f9; }
        h1 { color: #0f172a; }
        table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background: #0f172a; color: white; }
        tr:nth-child(even) { background: #f9f9f9; }
        .container { max-width: 1200px; margin: 0 auto; }
        .back-link { display: inline-block; margin-bottom: 1rem; color: #f59e0b; text-decoration: none; }
    </style>
</head>
<body>
<div class="container">
    <a href="../contact.html" class="back-link">← Back to Contact Page</a>
    <h1>Submitted Messages</h1>
    <?php
    $result = mysqli_query($conn, "SELECT * FROM messages ORDER BY created_at DESC");
    if (mysqli_num_rows($result) > 0) {
        echo "<table>";
        echo "<thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Message</th><th>Date</th></tr></thead>";
        echo "<tbody>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
            echo "<td>" . nl2br(htmlspecialchars($row['message'])) . "</td>";
            echo "<td>{$row['created_at']}</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p>No messages yet. Submit a message from the contact form.</p>";
    }
    ?>
</div>
</body>
</html>