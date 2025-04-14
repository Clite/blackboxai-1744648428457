<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

redirectIfNotLoggedIn();

if (!isTeacher() && !isAdmin()) {
    header('Location: ../index.php');
    exit();
}

// Fetch all exams created by this teacher or all exams if admin
if (isAdmin()) {
    $stmt = $pdo->query("SELECT id, title FROM exams ORDER BY created_at DESC");
} else {
    $stmt = $pdo->prepare("SELECT id, title FROM exams WHERE created_by = ? ORDER BY created_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
}
$exams = $stmt->fetchAll();

$selected_exam_id = $_GET['exam_id'] ?? null;
$performance = null;

if ($selected_exam_id) {
    // Fetch all attempts for the selected exam
    $stmt = $pdo->prepare("SELECT ea.id, ea.user_id, u.username, ea.score, ea.start_time FROM exam_attempts ea JOIN users u ON ea.user_id = u.id WHERE ea.exam_id = ?");
    $stmt->execute([$selected_exam_id]);
    $attempts = $stmt->fetchAll();

    if ($attempts) {
        $scores = array_column($attempts, 'score');
        $average_score = array_sum($scores) / count($scores);
        $highest_score = max($scores);
        $lowest_score = min($scores);

        $performance = [
            'attempts' => $attempts,
            'average_score' => $average_score,
            'highest_score' => $highest_score,
            'lowest_score' => $lowest_score,
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Exam Performance Analysis - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../assets/style.css" />
</head>
<body>
    <div class="container">
        <h1>Exam Performance Analysis</h1>
        <p><a href="../logout.php">Logout</a></p>

        <form method="GET" action="performance_analysis.php">
            <label for="exam_id">Select Exam:</label>
            <select name="exam_id" id="exam_id" required>
                <option value="">-- Select an exam --</option>
                <?php foreach ($exams as $exam): ?>
                    <option value="<?php echo $exam['id']; ?>" <?php if ($selected_exam_id == $exam['id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($exam['title']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">View Performance</button>
        </form>

        <?php if ($performance): ?>
            <h2>Performance Summary</h2>
            <p>Average Score: <?php echo number_format($performance['average_score'], 2); ?></p>
            <p>Highest Score: <?php echo $performance['highest_score']; ?></p>
            <p>Lowest Score: <?php echo $performance['lowest_score']; ?></p>

            <h3>Individual Attempts</h3>
            <table border="1" cellpadding="5" cellspacing="0">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Score</th>
                        <th>Start Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($performance['attempts'] as $attempt): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($attempt['username']); ?></td>
                            <td><?php echo $attempt['score']; ?></td>
                            <td><?php echo $attempt['start_time']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php elseif ($selected_exam_id): ?>
            <p>No attempts found for this exam.</p>
        <?php endif; ?>
    </div>
</body>
</html>
