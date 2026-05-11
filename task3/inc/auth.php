<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../inc/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}
?>