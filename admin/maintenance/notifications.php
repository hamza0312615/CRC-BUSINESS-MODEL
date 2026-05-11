<?php
// admin/maintenance/notifications.php
session_start();
require_once '../../config.php';

require_once __DIR__ . '/../admin_auth.php';

$stmt = $pdo->query("SELECT m.*, c.plate_number, c.name,
                     DATE_ADD(m.maintenance_date, INTERVAL m.next_due_days DAY) as due_date
                     FROM maintenance m
                     JOIN cars c ON m.car_id = c.id
                     WHERE DATE_ADD(m.maintenance_date, INTERVAL m.next_due_days DAY) <= CURDATE()
                     ORDER BY due_date ASC");
$dueMaintenances = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Notifications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <?php include '../navbar.php'; ?>
    <div class="container mt-4">
        <h2>Maintenance Due Alerts</h2>
        <a href="index.php" class="btn btn-secondary mb-3">Back to Maintenance</a>

        <?php if ($dueMaintenances): ?>
            <div class="row">
                <?php foreach ($dueMaintenances as $due): ?>
                    <div class="col-md-6 mb-3">
                        <div class="card alert-due shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($due['name'] . ' (' . $due['plate_number'] . ')'); ?></h5>
                                <p class="card-text mb-1"><strong>Type:</strong> <?php echo htmlspecialchars($due['type']); ?></p>
                                <p class="card-text mb-1"><strong>Last Done:</strong> <?php echo htmlspecialchars($due['maintenance_date']); ?></p>
                                <p class="card-text mb-0"><strong>Due Since:</strong> <?php echo htmlspecialchars($due['due_date']); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-success">No maintenance tasks are currently due!</div>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>