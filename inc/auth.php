<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/functions.php';
if (!isLoggedIn()) {
    redirect(BASE_URL . '/login.php');
}
?>