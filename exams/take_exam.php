<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isStudent()) {
    header('Location: ../login.php');
    exit();
}

// TODO: Fetch exam details and questions based on exam ID
$exam_id = $_GET['id'] ?? null;
if (!$exam_id) {
    die("No exam specified.");
}

// Fetch exam details
$stmt = $pdo->prepare("SELECT * FROM exams WHERE id = ?");
$stmt->execute([$exam_id]);
$exam = $stmt->fetch();

if (!$exam) {
    die("Exam not found.");
}

// Fetch questions for the exam
$stmt = $pdo->prepare("SELECT * FROM questions WHERE exam_id = ?");
$stmt->execute([$exam_id]);
$questions = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $exam['title']; ?> - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="container">
        <h1><?php echo $exam['title']; ?></h1>

        <?php if ($exam['is_proctored']): ?>
            <div class="proctored-notice" style="padding: 10px; background-color: #ffdddd; border: 1px solid #ff0000; margin-bottom: 20px;">
                <strong>Note:</strong> This is a proctored exam. Please ensure you follow all proctoring guidelines.
            </div>
        <?php endif; ?>

        <form method="POST" action="submit_exam.php">
            <input type="hidden" name="exam_id" value="<?php echo $exam_id; ?>">
            <?php foreach ($questions as $index => $question): ?>
                <div class="form-group">
                    <h3><?php echo ($index + 1) . ". " . $question['question_text']; ?></h3>
                    <input type="hidden" name="questions[<?php echo $index; ?>][id]" value="<?php echo $question['id']; ?>">
                    <input type="hidden" name="questions[<?php echo $index; ?>][type]" value="<?php echo $question['question_type']; ?>">
                    <?php if ($question['question_type'] == 'multiple_choice' || $question['question_type'] == 'single_choice'): ?>
                        <!-- Fetch options for multiple choice and single choice questions -->
                        <?php
                        $stmt = $pdo->prepare("SELECT * FROM options WHERE question_id = ?");
                        $stmt->execute([$question['id']]);
                        $options = $stmt->fetchAll();
                        foreach ($options as $option): ?>
                            <label>
                                <input type="radio" name="questions[<?php echo $index; ?>][answer]" value="<?php echo $option['id']; ?>">
                                <?php echo $option['option_text']; ?>
                            </label><br>
                        <?php endforeach; ?>
                    <?php elseif ($question['question_type'] == 'true_false'): ?>
                        <label>
                            <input type="radio" name="questions[<?php echo $index; ?>][answer]" value="true"> True
                        </label>
                        <label>
                            <input type="radio" name="questions[<?php echo $index; ?>][answer]" value="false"> False
                        </label>
                    <?php elseif ($question['question_type'] == 'fill_in_the_blanks'): ?>
                        <input type="text" name="questions[<?php echo $index; ?>][answer]" required placeholder="Fill in the blank">
                    <?php elseif ($question['question_type'] == 'essay'): ?>
                        <textarea name="questions[<?php echo $index; ?>][answer]" rows="6" required></textarea>
                    <?php elseif ($question['question_type'] == 'matching'): ?>
                        <!-- Matching question UI -->
                        <p>Matching question type is not yet implemented.</p>
                    <?php elseif ($question['question_type'] == 'drag_and_drop'): ?>
                        <!-- Drag and drop question UI -->
                        <p>Drag and drop question type is not yet implemented.</p>
                    <?php else: ?>
                        <textarea name="questions[<?php echo $index; ?>][answer]" required></textarea>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            <button type="submit" class="btn">Submit Exam</button>
        </form>
    </div>
</body>
</html>
