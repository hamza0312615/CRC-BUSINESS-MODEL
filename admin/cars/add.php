<?php
// admin/cars/add.php
session_start();
require_once '../../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $model = $_POST['model'] ?? '';
    $plate_number = $_POST['plate_number'] ?? '';
    $color = $_POST['color'] ?? '';
    $private_notes = $_POST['private_notes'] ?? '';

    if ($name && $plate_number) {
        // Check plate number
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM cars WHERE plate_number = ?");
        $stmt->execute([$plate_number]);
        if ($stmt->fetchColumn() > 0) {
            $error = 'Plate number already exists.';
        } else {
            $stmt = $pdo->prepare("INSERT INTO cars (name, model, plate_number, color, private_notes) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$name, $model, $plate_number, $color, $private_notes])) {
                $success = 'Car added successfully.';
            } else {
                $error = 'Failed to add car.';
            }
        }
    } else {
        $error = 'Name and Plate Number are required.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Car</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../navbar.php'; ?>
    <div class="container mt-4">
        <h2>Add New Car</h2>
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
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Model</label>
                        <input type="text" name="model" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Plate Number</label>
                        <input type="text" name="plate_number" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Color</label>
                        <input type="text" name="color" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Private Notes (Admin only)</label>
                        <textarea name="private_notes" class="form-control" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Car</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>