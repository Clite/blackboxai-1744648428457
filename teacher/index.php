<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isTeacher()) {
    header('Location: ../login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="container">
        <h1>Teacher Dashboard</h1>
        <nav>
            <a href="create_exam.php">Create New Exam</a>
            <a href="../logout.php">Logout</a>
        </nav>
        
        <h2>Your Exams</h2>
        <div class="exam-list">
            <!-- List of exams will go here -->
            <div class="exam-card">
                <h3>Sample Exam</h3>
                <p>This is a sample exam description</p>
                <a href="edit_exam.php?id=1">Edit</a>
                <a href="view_results.php?id=1">View Results</a>
            </div>
        </div>
    </div>
</body>
</html>
