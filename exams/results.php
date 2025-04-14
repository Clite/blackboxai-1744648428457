<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isStudent()) {
    header('Location: ../login.php');
    exit();
}

// Fetch the latest exam attempt for the user
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM exam_attempts WHERE user_id = ? ORDER BY start_time DESC LIMIT 1");
$stmt->execute([$user_id]);
$attempt = $stmt->fetch();

if (!$attempt) {
    die("No exam attempts found.");
}

// Fetch the exam details
$stmt = $pdo->prepare("SELECT * FROM exams WHERE id = ?");
$stmt->execute([$attempt['exam_id']]);
$exam = $stmt->fetch();

// Fetch the answers and calculate score
$stmt = $pdo->prepare("SELECT * FROM answers WHERE attempt_id = ?");
$stmt->execute([$attempt['id']]);
$answers = $stmt->fetchAll();

$score = 0;
$totalQuestions = count($answers);

foreach ($answers as $answer) {
    // Check if the answer is correct (this logic will depend on your implementation)
    // For now, we will assume a correct answer is stored in the database
    // You may need to adjust this based on your actual data structure

    // Fetch question type
    $stmt = $pdo->prepare("SELECT question_type FROM questions WHERE id = ?");
    $stmt->execute([$answer['question_id']]);
    $question_type = $stmt->fetchColumn();

    if (in_array($question_type, ['multiple_choice', 'single_choice', 'true_false'])) {
        $stmt = $pdo->prepare("SELECT is_correct FROM options WHERE question_id = ? AND id = ?");
        $stmt->execute([$answer['question_id'], $answer['answer_text']]);
        $is_correct = $stmt->fetchColumn();
        if ($is_correct) {
            $score++;
        }
    } elseif ($question_type == 'fill_in_the_blanks') {
        // Simple string comparison for fill in the blanks
        // You may want to improve this with case-insensitive or partial matching
        $stmt = $pdo->prepare("SELECT correct_answer FROM questions WHERE id = ?");
        $stmt->execute([$answer['question_id']]);
        $correct_answer = $stmt->fetchColumn();
        if (trim(strtolower($answer['answer_text'])) == trim(strtolower($correct_answer))) {
            $score++;
        }
    } elseif ($question_type == 'essay') {
        // Essay questions require manual grading, so skip scoring here
        continue;
    } elseif (in_array($question_type, ['matching', 'drag_and_drop'])) {
        // For matching and drag and drop, you may want to implement custom scoring logic
        // For now, skip scoring
        continue;
    }
}

$percentage = ($score / $totalQuestions) * 100;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Results - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="container">
        <h1>Results for <?php echo $exam['title']; ?></h1>
        <p>Your Score: <?php echo $score; ?> out of <?php echo $totalQuestions; ?> (<?php echo number_format($percentage, 2); ?>%)</p>
        <p><a href="index.php">Back to Exams</a></p>
    </div>
</body>
</html>
