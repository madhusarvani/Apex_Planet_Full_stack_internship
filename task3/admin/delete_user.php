<?php
require_once '../config/db.php';
require_once '../inc/auth.php';
require_once '../inc/functions.php';

if (!isAdmin()) {
    redirect('../dashboard.php');
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $user_id = (int)$_GET['id'];
    
    // Prevent self-deletion
    if ($user_id === $_SESSION['user_id']) {
        redirect('users.php?error=You cannot delete your own account');
    }
    
    // Get profile picture
    $stmt = $pdo->prepare("SELECT profile_picture FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    if ($user) {
        // Delete profile picture file if not default
        if ($user['profile_picture'] !== 'default.png' && file_exists("../uploads/" . $user['profile_picture'])) {
            unlink("../uploads/" . $user['profile_picture']);
        }
        
        // Delete user
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        
        redirect('users.php?success=User+deleted+successfully');
    } else {
        redirect('users.php?error=User+not+found');
    }
} else {
    redirect('users.php?error=Invalid+request');
}
?>