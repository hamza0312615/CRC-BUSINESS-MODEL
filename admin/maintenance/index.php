<?php
// admin/maintenance/index.php
session_start();
require_once '../../config.php';

require_once __DIR__ . '/../../includes/admin_auth.php';

$stmt = $pdo->query("SELECT m.*, c.name, c.plate_number FROM maintenance m JOIN cars c ON m.car_id = c.id ORDER BY m.maintenance_date DESC");
$records = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Records</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../navbar.php'; ?>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Maintenance Records</h2>
            <div>
                <a href="notifications.php" class="btn btn-warning me-2">Alerts</a>
                <a href="add.php" class="btn btn-primary">Add Record</a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Car</th>
                        <th>Type</th>
                        <th>Reading</th>
                        <th>Next Due (Days)</th>
                        <th>Photo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $rec): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($rec['maintenance_date']); ?></td>
                            <td><?php echo htmlspecialchars($rec['name'] . ' (' . $rec['plate_number'] . ')'); ?></td>
                            <td><?php echo htmlspecialchars($rec['type']); ?></td>
                            <td><?php echo htmlspecialchars($rec['reading_value']); ?></td>
                            <td><?php echo htmlspecialchars($rec['next_due_days']); ?></td>
                            <td>
                                <?php if ($rec['photo_path']): ?>
                                    <a href="../../<?php echo htmlspecialchars($rec['photo_path']); ?>" target="_blank" class="btn btn-sm btn-info">View</a>
                                <?php else: ?>
                                    No photo
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