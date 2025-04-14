<?php
// Exam taking interface
require_once '../includes/config.php';
require_once '../includes/auth.php';
redirectIfNotLoggedIn();
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
        <!-- Exams will be listed here -->
    </div>
    <p><a href="../logout.php">Logout</a></p>
</body>
</html>
