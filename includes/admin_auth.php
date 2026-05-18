<?php
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $rootDir = str_replace('\\', '/', dirname(__DIR__));
    $scriptFilename = str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME'] ?? '');

    $relativePath = str_replace($rootDir . '/', '', $scriptFilename);
    $depth = substr_count($relativePath, '/');

    $redirectPath = str_repeat('../', $depth) . 'index.php';

    header("Location: " . $redirectPath);
    exit;
}
