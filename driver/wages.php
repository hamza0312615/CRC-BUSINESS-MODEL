<?php
// driver/wages.php
session_start();
require_once '../config.php';

require_once __DIR__ . '/../includes/driver_auth.php';

$driver_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM wages WHERE driver_id = ? ORDER BY date DESC");
$stmt->execute([$driver_id]);
$wages = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT SUM(amount) FROM wages WHERE driver_id = ? AND is_paid = 0");
$stmt->execute([$driver_id]);
$unpaidTotal = $stmt->fetchColumn() ?: 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-4">
        <h2>My Wages</h2>
        <div class="alert alert-info mt-3">
            <strong>Total Pending: </strong> Rs. <?php echo number_format($unpaidTotal, 2); ?>
        </div>

        <div class="table-responsive mt-3">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Amount (Rs.)</th>
                        <th>Status</th>
                        <th>Paid Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($wages): ?>
                        <?php foreach ($wages as $wage): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($wage['date']); ?></td>
                                <td><?php echo number_format($wage['amount'], 2); ?></td>
                                <td>
                                    <?php if ($wage['is_paid']): ?>
                                        <span class="badge bg-success">Paid</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $wage['paid_date'] ? htmlspecialchars($wage['paid_date']) : '-'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center">No wages recorded yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>