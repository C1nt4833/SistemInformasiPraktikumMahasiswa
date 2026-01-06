<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check user role
function checkRole($requiredRole) {
    if (!isLoggedIn()) {
        header('Location: ../index.php');
        exit();
    }
    
    if ($_SESSION['role'] !== $requiredRole) {
        header('Location: ../unauthorized.php');
        exit();
    }
}

// Get current user data
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'nama' => $_SESSION['nama'],
        'email' => $_SESSION['email'],
        'role' => $_SESSION['role'],
        'nim' => $_SESSION['nim'] ?? null
    ];
}
?>

