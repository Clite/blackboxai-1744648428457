<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

session_start();

$user_role = $_SESSION['user_role'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Welcome - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/style.css" />
</head>
<body>
    <div class="container">
        <h1>Welcome to <?php echo APP_NAME; ?></h1>
        <?php if (isset($_SESSION['user_id'])): ?>
            <p>Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
            <nav>
                <ul>
                    <?php if ($user_role === 'admin'): ?>
                        <li><a href="admin/index.php">Admin Dashboard</a></li>
                    <?php endif; ?>
                    <?php if ($user_role === 'teacher'): ?>
                        <li><a href="teacher/index.php">Teacher Dashboard</a></li>
                    <?php endif; ?>
                    <?php if ($user_role === 'student'): ?>
                        <li><a href="exams/index.php">Take Exams</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        <?php else: ?>
            <p>Please <a href="login.php">login</a> or <a href="register.php">register</a> to continue.</p>
        <?php endif; ?>
    </div>
</body>
</html>
