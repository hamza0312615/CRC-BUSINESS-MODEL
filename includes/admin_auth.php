<?php
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $project_root = dirname(__DIR__);
    $script_dir = dirname($_SERVER['SCRIPT_FILENAME'] ?? __FILE__);

    // Calculate the relative depth securely by comparing slash counts
    $depth = substr_count(str_replace('\\', '/', $script_dir), '/') - substr_count(str_replace('\\', '/', $project_root), '/');
    $redirect_path = str_repeat('../', max(0, $depth)) . 'index.php';

    header("Location: " . $redirect_path);
    exit;
}
