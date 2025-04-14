<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isTeacher()) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle new question submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['question_text'], $_POST['question_type'], $_POST['points'])) {
    $question_text = $_POST['question_text'];
    $question_type = $_POST['question_type'];
    $points = intval($_POST['points']);

    // Insert question without exam_id (null) to represent question bank
    $stmt = $pdo->prepare("INSERT INTO questions (exam_id, question_text, question_type, points) VALUES (NULL, ?, ?, ?)");
    $stmt->execute([$question_text, $question_type, $points]);

    $_SESSION['success'] = "Question added to the question bank.";
    header('Location: question_bank.php');
    exit();
}

// Fetch all questions in the question bank (exam_id IS NULL)
$stmt = $pdo->query("SELECT * FROM questions WHERE exam_id IS NULL ORDER BY id DESC");
$questions = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Question Bank - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../assets/style.css" />
</head>
<body>
    <div class="container">
        <h1>Question Bank</h1>

        <?php if (isset($_SESSION['success'])): ?>
            <p class="success"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></p>
        <?php endif; ?>

        <h2>Add New Question</h2>
        <form method="POST">
            <label for="question_text">Question Text:</label><br>
            <textarea name="question_text" id="question_text" rows="4" required></textarea><br>

            <label for="question_type">Question Type:</label><br>
            <select name="question_type" id="question_type" required>
                <option value="multiple_choice">Multiple Choice</option>
                <option value="single_choice">Single Choice</option>
                <option value="fill_in_the_blanks">Fill in the Blanks</option>
                <option value="essay">Essay</option>
                <option value="matching">Matching</option>
                <option value="drag_and_drop">Drag and Drop</option>
                <option value="true_false">True/False</option>
                <option value="short_answer">Short Answer</option>
            </select><br>

            <label for="points">Points:</label><br>
            <input type="number" name="points" id="points" value="1" min="1" required><br><br>

            <button type="submit">Add Question</button>
        </form>

        <h2>Existing Questions</h2>
        <?php if (count($questions) > 0): ?>
            <ul>
                <?php foreach ($questions as $question): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($question['question_text']); ?></strong>
                        (<?php echo htmlspecialchars($question['question_type']); ?>, <?php echo $question['points']; ?> points)
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No questions in the question bank yet.</p>
        <?php endif; ?>

        <p><a href="index.php">Back to Teacher Dashboard</a></p>
    </div>
</body>
</html>
