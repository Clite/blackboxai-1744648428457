<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isTeacher()) {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // TODO: Process adding questions to the exam
    // This will include saving the question and its options to the database
    $_SESSION['success'] = "Question added successfully!";
    header('Location: create_exam.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Question - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="container">
        <h1>Add Question</h1>
        <form method="POST">
            <div class="form-group">
                <label for="question_text">Question Text:</label>
                <textarea id="question_text" name="question_text" required></textarea>
            </div>
            <div class="form-group">
                <label for="question_type">Question Type:</label>
                <select id="question_type" name="question_type" required>
                    <option value="multiple_choice">Multiple Choice</option>
                    <option value="true_false">True/False</option>
                    <option value="short_answer">Short Answer</option>
                </select>
            </div>
            <div class="form-group">
                <label for="points">Points:</label>
                <input type="number" id="points" name="points" min="1" value="1" required>
            </div>
            <button type="submit">Add Question</button>
        </form>
    </div>
</body>
</html>
