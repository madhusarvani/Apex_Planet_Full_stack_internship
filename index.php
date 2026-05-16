<?php
require_once 'config/db.php';
require_once 'inc/functions.php';

if (isLoggedIn()) {
    redirect(BASE_URL . (isAdmin() ? '/admin/dashboard.php' : '/customer/index.php'));
} else {
    redirect(BASE_URL . '/login.php');
}
?>