<?php
// admin/wages/index.php
session_start();
require_once '../../config.php';

require_once __DIR__ . '/../../includes/admin_auth.php';

$stmt = $pdo->query("SELECT w.*, u.name, u.username FROM wages w JOIN users u ON w.driver_id = u.id ORDER BY w.date DESC, w.created_at DESC");
$wages = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Wages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../navbar.php'; ?>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Wages</h2>
            <a href="add.php" class="btn btn-primary">Add Daily Wage</a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Driver</th>
                        <th>Amount (Rs.)</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($wages as $wage): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($wage['date']); ?></td>
                            <td><?php echo htmlspecialchars($wage['name'] . ' (' . $wage['username'] . ')'); ?></td>
                            <td><?php echo number_format($wage['amount'], 2); ?></td>
                            <td>
                                <?php if ($wage['is_paid']): ?>
                                    <span class="badge bg-success">Paid (<?php echo htmlspecialchars($wage['paid_date']); ?>)</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Unpaid</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!$wage['is_paid']): ?>
                                    <a href="mark_paid.php?id=<?php echo $wage['id']; ?>" class="btn btn-sm btn-success" onclick="return confirm('Mark this wage as paid?');">Mark Paid</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>