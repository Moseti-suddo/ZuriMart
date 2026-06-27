<?php
// ========================================
// AUTHENTICATION AND ROLE CHECKER
// ========================================
// Include this file at the top of any page
// that requires a logged-in user.
//
// Usage:
//   require_once 'includes/auth.php';           — any logged in user
//   requireRole('admin');                        — admin only
//   requireRole(['admin', 'manager']);           — admin or manager

function requireLogin() {
    // Make sure session is started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // If no user_id in session, they are not logged in
    if (!isset($_SESSION['user_id'])) {
        // Save the page they were trying to reach
        // so we can redirect them back after login
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header('Location: /shop/login.php');
        exit;
    }
}

function requireRole($roles) {
    // First make sure they are logged in at all
    requireLogin();

    // Allow passing a single role as string or multiple as array
    if (is_string($roles)) {
        $roles = [$roles];
    }

    // Check if their role matches any of the allowed roles
    if (!in_array($_SESSION['role'], $roles)) {
        // They are logged in but don't have permission
        // Send them to an access denied page
        header('Location: /shop/includes/denied.php');
        exit;
    }
}