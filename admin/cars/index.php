<?php
// admin/cars/index.php
session_start();
require_once '../../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit;
}

$stmt = $pdo->query("SELECT * FROM cars ORDER BY created_at DESC");
$cars = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Cars</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../navbar.php'; ?>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Cars</h2>
            <a href="add.php" class="btn btn-primary">Add Car</a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Model</th>
                        <th>Plate Number</th>
                        <th>Color</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cars as $car): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($car['name']); ?></td>
                            <td><?php echo htmlspecialchars($car['model']); ?></td>
                            <td><?php echo htmlspecialchars($car['plate_number']); ?></td>
                            <td><?php echo htmlspecialchars($car['color']); ?></td>
                            <td>
                                <span class="badge bg-<?php
                                    echo $car['status'] === 'Available' ? 'success' :
                                        ($car['status'] === 'Assigned' ? 'primary' : 'warning');
                                ?>">
                                    <?php echo htmlspecialchars($car['status']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="edit.php?id=<?php echo $car['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                <?php if ($car['status'] === 'Available'): ?>
                                    <a href="assign.php?id=<?php echo $car['id']; ?>" class="btn btn-sm btn-info">Assign</a>
                                <?php elseif ($car['status'] === 'Assigned'): ?>
                                    <a href="unassign.php?id=<?php echo $car['id']; ?>" class="btn btn-sm btn-secondary" onclick="return confirm('Unassign this car?');">Unassign</a>
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