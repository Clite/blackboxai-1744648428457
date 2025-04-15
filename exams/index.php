<?php
// Exam taking interface
require_once '../includes/config.php';
require_once '../includes/auth.php';
redirectIfNotLoggedIn();

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'] ?? '';

if ($user_role === 'student') {
    // Fetch exams available for the student
    $stmt = $pdo->prepare("SELECT * FROM exams WHERE (start_time IS NULL OR start_time <= NOW()) AND (end_time IS NULL OR end_time >= NOW()) ORDER BY start_time DESC");
    $stmt->execute();
    $exams = $stmt->fetchAll();
} elseif ($user_role === 'teacher') {
    // Fetch exams created by the teacher
    $stmt = $pdo->prepare("SELECT * FROM exams WHERE created_by = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    $exams = $stmt->fetchAll();
} else {
    // For other roles, show all exams
    $stmt = $pdo->query("SELECT * FROM exams ORDER BY created_at DESC");
    $exams = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exams - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <h1>Available Exams</h1>
    <div class="exam-list">
        <?php if (count($exams) > 0): ?>
            <ul>
                <?php foreach ($exams as $exam): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($exam['title']); ?></strong>
                        <?php if ($user_role === 'student'): ?>
                            - <a href="take_exam.php?id=<?php echo $exam['id']; ?>">Take Exam</a>
                            - <a href="results.php?exam_id=<?php echo $exam['id']; ?>">View Results</a>
                        <?php elseif ($user_role === 'teacher'): ?>
                            - <a href="../teacher/create_exam.php?edit=<?php echo $exam['id']; ?>">Edit Exam</a>
                        <?php else: ?>
                            - <a href="take_exam.php?id=<?php echo $exam['id']; ?>">Take Exam</a>
                            - <a href="results.php?exam_id=<?php echo $exam['id']; ?>">View Results</a>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No exams available.</p>
        <?php endif; ?>
    </div>
    <p><a href="../logout.php">Logout</a></p>
</body>
</html>
