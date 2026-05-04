<?php
// admin/drivers/edit.php
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
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'driver' AND is_deleted = 0");
$stmt->execute([$id]);
$driver = $stmt->fetch();

if (!$driver) {
    header("Location: index.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $cnic = $_POST['cnic'] ?? '';
    $joining_date = $_POST['joining_date'] ?? '';

    if ($name) {
        $stmt = $pdo->prepare("UPDATE users SET name = ?, phone = ?, cnic = ?, joining_date = ? WHERE id = ?");
        if ($stmt->execute([$name, $phone, $cnic, $joining_date, $id])) {
            $success = 'Driver updated successfully.';
            $driver['name'] = $name;
            $driver['phone'] = $phone;
            $driver['cnic'] = $cnic;
            $driver['joining_date'] = $joining_date;
        } else {
            $error = 'Failed to update driver.';
        }
    } else {
        $error = 'Name is required.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Driver</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../navbar.php'; ?>
    <div class="container mt-4">
        <h2>Edit Driver: <?php echo htmlspecialchars($driver['name']); ?></h2>
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
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($driver['name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Username (Cannot be changed)</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($driver['username']); ?>" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($driver['phone'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">CNIC</label>
                        <input type="text" name="cnic" class="form-control" value="<?php echo htmlspecialchars($driver['cnic'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Joining Date</label>
                        <input type="date" name="joining_date" class="form-control" value="<?php echo htmlspecialchars($driver['joining_date'] ?? ''); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Update Driver</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>