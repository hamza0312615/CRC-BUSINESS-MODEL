<?php
// admin/drivers/calls.php
session_start();
require_once '../../config.php';

require_once __DIR__ . '/../../includes/admin_auth.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT id, name FROM users WHERE id = ? AND role = 'driver' AND is_deleted = 0");
$stmt->execute([$id]);
$driver = $stmt->fetch();

if (!$driver) {
    header("Location: index.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $call_date = $_POST['call_date'] ?? date('Y-m-d');
    $notes = $_POST['notes'] ?? '';

    if ($notes) {
        $stmt = $pdo->prepare("INSERT INTO driver_calls (driver_id, call_date, notes) VALUES (?, ?, ?)");
        if ($stmt->execute([$id, $call_date, $notes])) {
            $success = 'Call logged successfully.';
        } else {
            $error = 'Failed to log call.';
        }
    } else {
        $error = 'Notes are required.';
    }
}

// Fetch existing calls
$stmt = $pdo->prepare("SELECT * FROM driver_calls WHERE driver_id = ? ORDER BY call_date DESC, created_at DESC");
$stmt->execute([$id]);
$calls = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Call Logs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../navbar.php'; ?>
    <div class="container mt-4">
        <h2>Call Logs for: <?php echo htmlspecialchars($driver['name']); ?></h2>
        <a href="index.php" class="btn btn-secondary mb-3">Back to List</a>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Log New Call</h5>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" name="call_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Log Call</button>
                </form>
            </div>
        </div>

        <h4>Previous Calls</h4>
        <?php if ($calls): ?>
            <div class="list-group">
                <?php foreach ($calls as $call): ?>
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">Date: <?php echo htmlspecialchars($call['call_date']); ?></h6>
                            <small class="text-muted">Logged on: <?php echo htmlspecialchars($call['created_at']); ?></small>
                        </div>
                        <p class="mb-1"><?php echo nl2br(htmlspecialchars($call['notes'])); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No calls logged yet.</p>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>