<?php
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'Admin';
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function uploadProfilePicture($file, $existingImage = 'default.png') {
    $targetDir = "uploads/";
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxSize = 2 * 1024 * 1024; // 2MB
    
    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return $existingImage;
    }
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    if (!in_array($file['type'], $allowedTypes)) {
        return false;
    }
    
    if ($file['size'] > $maxSize) {
        return false;
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = time() . '_' . uniqid() . '.' . $extension;
    $targetPath = $targetDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        if ($existingImage !== 'default.png' && file_exists($targetDir . $existingImage)) {
            unlink($targetDir . $existingImage);
        }
        return $filename;
    }
    return false;
}

function getProfileImageUrl($imageName) {
    // If user has uploaded a custom image and it exists, return it
    if ($imageName && $imageName !== 'default.png' && file_exists("uploads/" . $imageName)) {
        return "uploads/" . $imageName;
    }
    
    // Modern SVG silhouette (gray circle with person icon) – like WhatsApp/Instagram
    return "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ccircle cx='50' cy='50' r='50' fill='%23e2e8f0'/%3E%3Cpath fill='%2394a3b8' d='M50 55c-12 0-22 10-22 22v5h44v-5c0-12-10-22-22-22z'/%3E%3Ccircle fill='%2394a3b8' cx='50' cy='35' r='14'/%3E%3C/svg%3E";
}