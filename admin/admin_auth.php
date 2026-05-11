<?php
// admin/admin_auth.php

// Protect admin pages
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Determine the depth of the current script relative to the admin directory
    // to build the correct relative path to the root index.php
    $admin_dir = realpath(__DIR__);
    $current_dir = dirname($_SERVER['SCRIPT_FILENAME']);

    // Normalize paths to forward slashes for reliable comparison
    $admin_dir = str_replace('\\', '/', $admin_dir);
    $current_dir = str_replace('\\', '/', $current_dir);

    $redirect = '../index.php'; // Default fallback

    if (strpos($current_dir, $admin_dir) === 0) {
        $diff = substr($current_dir, strlen($admin_dir));
        $depth = $diff ? substr_count(trim($diff, '/'), '/') + 1 : 0;
        $redirect = str_repeat('../', $depth + 1) . 'index.php';
    }

    header("Location: " . $redirect);
    exit;
}
