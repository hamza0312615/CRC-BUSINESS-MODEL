<?php
// admin/wages/add.php
session_start();
require_once '../../config.php';

require_once __DIR__ . '/../../includes/admin_auth.php';

$stmt = $pdo->query("SELECT id, name, username FROM users WHERE role = 'driver' AND is_deleted = 0 ORDER BY name");
$drivers = $stmt->fetchAll();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $driver_id = $_POST['driver_id'] ?? '';
    $date = $_POST['date'] ?? date('Y-m-d');
    $amount = $_POST['amount'] ?? 3200;

    if ($driver_id && $date && $amount > 0) {
        $stmt = $pdo->prepare("INSERT INTO wages (driver_id, date, amount) VALUES (?, ?, ?)");
        if ($stmt->execute([$driver_id, $date, $amount])) {
            $success = 'Wage recorded successfully.';
        } else {
            $error = 'Failed to record wage.';
        }
    } else {
        $error = 'Driver, Date, and valid Amount are required.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Wage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../navbar.php'; ?>
    <div class="container mt-4">
        <h2>Add Daily Wage</h2>
        <a href="index.php" class="btn btn-secondary mb-3">Back to List</a>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Driver</label>
                        <select name="driver_id" class="form-select" required>
                            <option value="">Select Driver</option>
                            <?php foreach ($drivers as $driver): ?>
                                <option value="<?php echo $driver['id']; ?>">
                                    <?php echo htmlspecialchars($driver['name'] . ' (' . $driver['username'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" name="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount (Rs.)</label>
                        <input type="number" step="0.01" name="amount" class="form-control" value="3200" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Wage</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>