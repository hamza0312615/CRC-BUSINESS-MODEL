<?php
// admin/drivers/index.php
session_start();
require_once '../../config.php';

require_once __DIR__ . '/../admin_auth.php';

// Handle Soft Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("UPDATE users SET is_deleted = 1 WHERE id = ? AND role = 'driver'");
    $stmt->execute([$id]);
    header("Location: index.php");
    exit;
}

$stmt = $pdo->query("SELECT * FROM users WHERE role = 'driver' AND is_deleted = 0 ORDER BY created_at DESC");
$drivers = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Drivers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../navbar.php'; ?>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Drivers</h2>
            <a href="add.php" class="btn btn-primary">Add Driver</a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Phone</th>
                        <th>CNIC</th>
                        <th>Grade</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($drivers as $driver): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($driver['name']); ?></td>
                            <td><?php echo htmlspecialchars($driver['username']); ?></td>
                            <td><?php echo htmlspecialchars($driver['phone']); ?></td>
                            <td><?php echo htmlspecialchars($driver['cnic']); ?></td>
                            <td><?php echo htmlspecialchars($driver['grade']); ?></td>
                            <td>
                                <a href="edit.php?id=<?php echo $driver['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="grade.php?id=<?php echo $driver['id']; ?>" class="btn btn-sm btn-info">Grade</a>
                                <a href="calls.php?id=<?php echo $driver['id']; ?>" class="btn btn-sm btn-secondary">Calls</a>
                                <a href="index.php?delete=<?php echo $driver['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this driver?');">Delete</a>
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