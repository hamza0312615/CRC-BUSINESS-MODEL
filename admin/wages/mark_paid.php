<?php
// admin/wages/mark_paid.php
session_start();
require_once '../../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $paid_date = date('Y-m-d');

    $stmt = $pdo->prepare("UPDATE wages SET is_paid = 1, paid_date = ? WHERE id = ?");
    $stmt->execute([$paid_date, $id]);
}

header("Location: index.php");
exit;
?>