<?php
// admin/drivers/grade.php
session_start();
require_once '../../config.php';

require_once __DIR__ . '/../admin_auth.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT id, name, grade, grade_comments FROM users WHERE id = ? AND role = 'driver' AND is_deleted = 0");
$stmt->execute([$id]);
$driver = $stmt->fetch();

if (!$driver) {
    header("Location: index.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $grade = $_POST['grade'] ?? '';
    $grade_comments = $_POST['grade_comments'] ?? '';

    $stmt = $pdo->prepare("UPDATE users SET grade = ?, grade_comments = ? WHERE id = ?");
    if ($stmt->execute([$grade, $grade_comments, $id])) {
        $success = 'Grade updated successfully.';
        $driver['grade'] = $grade;
        $driver['grade_comments'] = $grade_comments;
    } else {
        $error = 'Failed to update grade.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grade Driver</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../navbar.php'; ?>
    <div class="container mt-4">
        <h2>Grade Driver: <?php echo htmlspecialchars($driver['name']); ?></h2>
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
                        <label class="form-label">Grade</label>
                        <select name="grade" class="form-select">
                            <option value="">Select Grade</option>
                            <option value="A" <?php echo ($driver['grade'] === 'A') ? 'selected' : ''; ?>>A - Excellent</option>
                            <option value="B" <?php echo ($driver['grade'] === 'B') ? 'selected' : ''; ?>>B - Good</option>
                            <option value="C" <?php echo ($driver['grade'] === 'C') ? 'selected' : ''; ?>>C - Average</option>
                            <option value="D" <?php echo ($driver['grade'] === 'D') ? 'selected' : ''; ?>>D - Poor</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Comments</label>
                        <textarea name="grade_comments" class="form-control" rows="4"><?php echo htmlspecialchars($driver['grade_comments'] ?? ''); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Grade</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>