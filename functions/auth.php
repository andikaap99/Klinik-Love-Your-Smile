<?php
session_start();
require __DIR__ . '/permissions.php';

// Get current page filename
$current_page = basename($_SERVER['PHP_SELF']);

// Allow public pages
if (in_array($current_page, $public_pages)) {
    return; // No authentication needed
}

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if user has permission for this page
if (!in_array($current_page, $role_permissions[$_SESSION['user_type']])) {
    error_log("Permission denied for {$_SESSION['user_type']} on $current_page");
    header("Location: unauthorized.php");
    exit();
}
// Optional: Track last activity for session timeout
$_SESSION['last_activity'] = time();
?>