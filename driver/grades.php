<?php
// driver/grades.php
session_start();
require_once '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'driver') {
    header("Location: ../index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT grade, grade_comments FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Grades</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-4">
        <h2>My Performance Grade</h2>
        <div class="card mt-3">
            <div class="card-body">
                <h4 class="card-title">Current Grade:
                    <?php
                        if ($user['grade']) {
                            echo htmlspecialchars($user['grade']);
                        } else {
                            echo '<span class="text-muted">Not Graded Yet</span>';
                        }
                    ?>
                </h4>
                <p class="mt-3"><strong>Admin Comments:</strong></p>
                <p><?php echo nl2br(htmlspecialchars($user['grade_comments'] ?: 'No comments available.')); ?></p>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>