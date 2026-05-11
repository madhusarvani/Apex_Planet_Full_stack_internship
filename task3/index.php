<?php
require_once 'config/db.php';
require_once 'inc/functions.php';

if (isLoggedIn()) {
    redirect('dashboard.php');
} else {
    redirect('login.php');
}
?>