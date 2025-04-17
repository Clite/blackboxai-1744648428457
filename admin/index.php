<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

redirectIfNotLoggedIn();

if (!isAdmin()) {
    header('Location: ../index.php');
    exit();
}

require_once '../includes/auth.php';
redirectIfNotLoggedIn();

if (!isAdmin()) {
    header('Location: ../index.php');
    exit();
}

// Fetch all goats
$goats = $pdo->query("SELECT * FROM goats")->fetchAll(PDO::FETCH_ASSOC);
$stmt = $pdo->query("SELECT id, username, email, role FROM users ORDER BY id DESC");
$users = $stmt->fetchAll();

// Fetch all exams
$stmt = $pdo->query("SELECT id, title, created_by FROM exams ORDER BY id DESC");
$exams = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Dashboard - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../assets/style.css" />
</head>
<body>
    <div class="container">
        <h1>Admin Dashboard</h1>
        <p><a href="../logout.php">Logout</a></p>

        <h2>Users</h2>
        <ul>
            <?php foreach ($users as $user): ?>
                <li><?php echo htmlspecialchars($user['username']) . ' (' . htmlspecialchars($user['role']) . ')'; ?></li>
            <?php endforeach; ?>
        </ul>

        <h2>Exams</h2>
        <ul>
            <?php foreach ($exams as $exam): ?>
                <li><?php echo htmlspecialchars($exam['title']); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>
