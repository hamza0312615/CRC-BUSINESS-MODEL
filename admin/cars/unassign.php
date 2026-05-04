<?php
// admin/cars/unassign.php
session_start();
require_once '../../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$car_id = (int)$_GET['id'];

try {
    $pdo->beginTransaction();

    // End assignment
    $stmt = $pdo->prepare("UPDATE assignments SET end_date = CURRENT_DATE() WHERE car_id = ? AND end_date IS NULL");
    $stmt->execute([$car_id]);

    // Update car status to Available
    $stmt = $pdo->prepare("UPDATE cars SET status = 'Available' WHERE id = ?");
    $stmt->execute([$car_id]);

    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
}

header("Location: index.php");
exit;
?>