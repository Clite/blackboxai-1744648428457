<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isStudent()) {
    header('Location: ../login.php');
    exit();
}

// Fetch exams available for the student
$stmt = $pdo->query("SELECT * FROM exams");
$exams = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Student Dashboard - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../assets/style.css" />
</head>
<body>
    <div class="container">
        <h1>Student Dashboard</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
        <h2>Available Exams</h2>
        <?php if (count($exams) > 0): ?>
            <ul>
                <?php foreach ($exams as $exam): ?>
                    <li>
                        <a href="../exams/take_exam.php?id=<?php echo $exam['id']; ?>">
                            <?php echo htmlspecialchars($exam['title']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No exams available at the moment.</p>
        <?php endif; ?>
        <p><a href="../logout.php">Logout</a></p>
    </div>
</body>
</html>
