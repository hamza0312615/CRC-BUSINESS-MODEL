<?php
// driver/dashboard.php
session_start();
require_once '../config.php';
require_once '../includes/driver_functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'driver') {
    header("Location: ../index.php");
    exit;
}

$driver_id = $_SESSION['user_id'];

// Get unpaid wages total
$stmt = $pdo->prepare("SELECT SUM(amount) FROM wages WHERE driver_id = ? AND is_paid = 0");
$stmt->execute([$driver_id]);
$unpaidWages = $stmt->fetchColumn() ?: 0;

// Get current car
$currentCar = get_current_assigned_car($pdo, $driver_id);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-4">
        <h2>Dashboard</h2>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card text-white bg-info mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Assigned Car</h5>
                        <p class="card-text fs-4">
                            <?php
                            if ($currentCar) {
                                echo htmlspecialchars($currentCar['name'] . ' (' . $currentCar['plate_number'] . ')');
                            } else {
                                echo 'No car currently assigned';
                            }
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Pending Wages to Receive</h5>
                        <p class="card-text fs-4">Rs. <?php echo number_format($unpaidWages, 2); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>