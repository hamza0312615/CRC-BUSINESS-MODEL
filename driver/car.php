<?php
// driver/car.php
session_start();
require_once '../config.php';

require_once __DIR__ . '/../includes/driver_auth.php';

$driver_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT c.name, c.model, c.plate_number, c.color, a.start_date
    FROM assignments a
    JOIN cars c ON a.car_id = c.id
    WHERE a.driver_id = ? AND a.end_date IS NULL
");
$stmt->execute([$driver_id]);
$car = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Assigned Car</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-4">
        <h2>My Assigned Car</h2>
        <?php if ($car): ?>
            <div class="card mt-3">
                <div class="card-body">
                    <p><strong>Car Name:</strong> <?php echo htmlspecialchars($car['name']); ?></p>
                    <p><strong>Model:</strong> <?php echo htmlspecialchars($car['model']); ?></p>
                    <p><strong>Plate Number:</strong> <?php echo htmlspecialchars($car['plate_number']); ?></p>
                    <p><strong>Color:</strong> <?php echo htmlspecialchars($car['color']); ?></p>
                    <p><strong>Assigned Since:</strong> <?php echo htmlspecialchars($car['start_date']); ?></p>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info mt-3">You do not have a car assigned currently.</div>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>