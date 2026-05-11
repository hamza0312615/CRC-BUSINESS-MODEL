<?php
// admin/dashboard.php
session_start();
require_once '../config.php';

// Protect admin pages
require_once __DIR__ . '/../includes/admin_auth.php';

// Stats
$stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'driver' AND is_deleted = 0");
$totalDrivers = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM cars");
$totalCars = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT SUM(amount) FROM wages WHERE is_paid = 0");
$unpaidWages = $stmt->fetchColumn() ?: 0;

// Maintenance Due
$stmt = $pdo->query("SELECT m.*, c.plate_number, c.name FROM maintenance m
                     JOIN cars c ON m.car_id = c.id
                     WHERE DATE_ADD(m.maintenance_date, INTERVAL m.next_due_days DAY) <= CURDATE()");
$dueMaintenances = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-4">
        <h2>Dashboard</h2>

        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Drivers</h5>
                        <p class="card-text fs-3"><?php echo $totalDrivers; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Cars</h5>
                        <p class="card-text fs-3"><?php echo $totalCars; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Unpaid Wages</h5>
                        <p class="card-text fs-3">Rs. <?php echo number_format($unpaidWages, 2); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($dueMaintenances): ?>
            <div class="alert alert-danger mt-4">
                <h4>Maintenance Due Alerts</h4>
                <ul>
                    <?php foreach ($dueMaintenances as $due): ?>
                        <li>
                            Car: <?php echo htmlspecialchars($due['name'] . ' (' . $due['plate_number'] . ')'); ?>
                            - Last Maintenance: <?php echo htmlspecialchars($due['maintenance_date']); ?>
                            (Due for <?php echo htmlspecialchars($due['type']); ?>)
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>