<?php
// admin/maintenance/add.php
session_start();
require_once '../../config.php';

require_once __DIR__ . '/../admin_auth.php';

$stmt = $pdo->query("SELECT id, name, plate_number FROM cars ORDER BY name");
$cars = $stmt->fetchAll();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $car_id = $_POST['car_id'] ?? '';
    $type = $_POST['type'] ?? '';
    $reading_value = $_POST['reading_value'] ?? 0;
    $maintenance_date = $_POST['maintenance_date'] ?? date('Y-m-d');
    $next_due_days = $_POST['next_due_days'] ?? 0;

    $photo_path = null;

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../uploads/maintenance/'; if (!is_dir($uploadDir)) { mkdir($uploadDir, 0777, true); }
        $fileName = time() . '_' . basename($_FILES['photo']['name']);
        $targetFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
            $photo_path = 'uploads/maintenance/' . $fileName;
        } else {
            $error = 'Failed to upload photo.';
        }
    }

    if ($car_id && $type && !$error) {
        $stmt = $pdo->prepare("INSERT INTO maintenance (car_id, type, reading_value, photo_path, maintenance_date, next_due_days) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$car_id, $type, $reading_value, $photo_path, $maintenance_date, $next_due_days])) {
            $success = 'Maintenance record added successfully.';
        } else {
            $error = 'Failed to add record.';
        }
    } elseif (!$error) {
        $error = 'Car and Maintenance Type are required.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Maintenance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../navbar.php'; ?>
    <div class="container mt-4">
        <h2>Add Maintenance Record</h2>
        <a href="index.php" class="btn btn-secondary mb-3">Back to List</a>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Car</label>
                        <select name="car_id" class="form-select" required>
                            <option value="">Select Car</option>
                            <?php foreach ($cars as $car): ?>
                                <option value="<?php echo $car['id']; ?>">
                                    <?php echo htmlspecialchars($car['name'] . ' (' . $car['plate_number'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type (e.g., Oil Change, Tyre)</label>
                        <input type="text" name="type" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reading Value (Meter)</label>
                        <input type="number" name="reading_value" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Maintenance Date</label>
                        <input type="date" name="maintenance_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Next Due (in Days)</label>
                        <input type="number" name="next_due_days" class="form-control" value="30" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Photo (Meter reading/Receipt)</label>
                        <input type="file" name="photo" class="form-control" accept="image/*">
                    </div>
                    <button type="submit" class="btn btn-primary">Add Record</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>