<?php
// admin/cars/edit.php
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

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ?");
$stmt->execute([$id]);
$car = $stmt->fetch();

if (!$car) {
    header("Location: index.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $model = $_POST['model'] ?? '';
    $color = $_POST['color'] ?? '';
    $status = $_POST['status'] ?? '';
    $private_notes = $_POST['private_notes'] ?? '';

    if ($name && $status) {
        $stmt = $pdo->prepare("UPDATE cars SET name = ?, model = ?, color = ?, status = ?, private_notes = ? WHERE id = ?");
        if ($stmt->execute([$name, $model, $color, $status, $private_notes, $id])) {
            $success = 'Car updated successfully.';
            $car['name'] = $name;
            $car['model'] = $model;
            $car['color'] = $color;
            $car['status'] = $status;
            $car['private_notes'] = $private_notes;
            @unlink(sys_get_temp_dir() . '/dashboard_cache.json');
        } else {
            $error = 'Failed to update car.';
        }
    } else {
        $error = 'Name and Status are required.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Car</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../navbar.php'; ?>
    <div class="container mt-4">
        <h2>Edit Car: <?php echo htmlspecialchars($car['plate_number']); ?></h2>
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
                        <label class="form-label">Car Name</label>
                        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($car['name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Model</label>
                        <input type="text" name="model" class="form-control" value="<?php echo htmlspecialchars($car['model'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Plate Number (Cannot be changed)</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($car['plate_number']); ?>" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Color</label>
                        <input type="text" name="color" class="form-control" value="<?php echo htmlspecialchars($car['color'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="Available" <?php echo ($car['status'] === 'Available') ? 'selected' : ''; ?>>Available</option>
                            <option value="Assigned" <?php echo ($car['status'] === 'Assigned') ? 'selected' : ''; ?>>Assigned</option>
                            <option value="In Maintenance" <?php echo ($car['status'] === 'In Maintenance') ? 'selected' : ''; ?>>In Maintenance</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Private Notes (Admin only)</label>
                        <textarea name="private_notes" class="form-control" rows="4"><?php echo htmlspecialchars($car['private_notes'] ?? ''); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Car</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>