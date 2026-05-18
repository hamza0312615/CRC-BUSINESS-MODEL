<?php
// admin/cars/assign.php
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
$stmt = $pdo->prepare("SELECT id, name, plate_number, status FROM cars WHERE id = ?");
$stmt->execute([$car_id]);
$car = $stmt->fetch();

if (!$car || $car['status'] !== 'Available') {
    die("Car not found or not available for assignment.");
}

$error = '';
$success = '';

// Fetch available drivers (drivers not currently having an active assignment)
$stmt = $pdo->query("
    SELECT u.id, u.name, u.username
    FROM users u
    WHERE u.role = 'driver' AND u.is_deleted = 0
    AND u.id NOT IN (
        SELECT driver_id FROM assignments WHERE end_date IS NULL
    )
");
$availableDrivers = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $driver_id = $_POST['driver_id'] ?? '';
    $start_date = $_POST['start_date'] ?? date('Y-m-d');

    if ($driver_id && $start_date) {
        try {
            $pdo->beginTransaction();

            // Insert assignment
            $stmt = $pdo->prepare("INSERT INTO assignments (driver_id, car_id, start_date) VALUES (?, ?, ?)");
            $stmt->execute([$driver_id, $car_id, $start_date]);

            // Update car status
            $stmt = $pdo->prepare("UPDATE cars SET status = 'Assigned' WHERE id = ?");
            $stmt->execute([$car_id]);

            @unlink(sys_get_temp_dir() . '/dashboard_cache.json');

            $pdo->commit();
            header("Location: index.php");
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Failed to assign car: " . $e->getMessage();
        }
    } else {
        $error = "Driver and Start Date are required.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Car</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../navbar.php'; ?>
    <div class="container mt-4">
        <h2>Assign Car: <?php echo htmlspecialchars($car['name'] . ' (' . $car['plate_number'] . ')'); ?></h2>
        <a href="index.php" class="btn btn-secondary mb-3">Back to List</a>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (empty($availableDrivers)): ?>
            <div class="alert alert-warning">No available drivers found. All active drivers already have a car assigned.</div>
        <?php else: ?>
            <div class="card">
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Select Driver</label>
                            <select name="driver_id" class="form-select" required>
                                <option value="">-- Select a driver --</option>
                                <?php foreach ($availableDrivers as $driver): ?>
                                    <option value="<?php echo $driver['id']; ?>">
                                        <?php echo htmlspecialchars($driver['name'] . ' (' . $driver['username'] . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Assign Car</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>