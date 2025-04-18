<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isTeacher()) {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $duration = $_POST['duration'];
    $start_time = !empty($_POST['start_time']) ? $_POST['start_time'] : null;
    $end_time = !empty($_POST['end_time']) ? $_POST['end_time'] : null;
    $is_proctored = isset($_POST['is_proctored']) ? 1 : 0;
    
    // Insert exam into the database
    $stmt = $pdo->prepare("INSERT INTO exams (title, description, duration_minutes, start_time, end_time, is_proctored, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $description, $duration, $start_time, $end_time, $is_proctored, $_SESSION['user_id']]);
    
    $exam_id = $pdo->lastInsertId();
    
    // Process questions
    if (isset($_POST['questions'])) {
        foreach ($_POST['questions'] as $question) {
            $question_text = $question['text'];
            $question_type = $question['type'];
            $points = $question['points'];
            
            // Insert question into the database
            $stmt = $pdo->prepare("INSERT INTO questions (exam_id, question_text, question_type, points) VALUES (?, ?, ?, ?)");
            $stmt->execute([$exam_id, $question_text, $question_type, $points]);
        }
    }
    
    $_SESSION['success'] = "Exam created successfully!";
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Exam - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="container">
        <h1>Create New Exam</h1>
        <form method="POST">
            <div class="form-group">
                <label for="title">Exam Title:</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label for="duration">Duration (minutes):</label>
                <input type="number" id="duration" name="duration" min="1" value="30" required>
            </div>

            <div class="form-group">
                <label for="start_time">Start Time:</label>
                <input type="datetime-local" id="start_time" name="start_time">
            </div>

            <div class="form-group">
                <label for="end_time">End Time:</label>
                <input type="datetime-local" id="end_time" name="end_time">
            </div>

            <div class="form-group">
                <label for="is_proctored">
                    <input type="checkbox" id="is_proctored" name="is_proctored" value="1">
                    Proctored Exam
                </label>
            </div>
            
            <h2>Questions</h2>
            <div id="questions-container">
                <!-- Questions will be added here via JavaScript -->
            </div>
            
            <button type="button" id="add-question" class="btn">Add Question</button>
            <button type="submit" class="btn">Create Exam</button>
        </form>
    </div>

    <script>
        document.getElementById('add-question').addEventListener('click', function() {
            const container = document.getElementById('questions-container');
            const questionCount = container.querySelectorAll('.question-card').length + 1;
            
            const questionDiv = document.createElement('div');
            questionDiv.className = 'question-card';
            questionDiv.innerHTML = `
                <h3>Question ${questionCount}</h3>
                <div class="form-group">
                    <label>Question Text:</label>
                    <textarea name="questions[${questionCount}][text]" required></textarea>
                </div>
                <div class="form-group">
                    <label>Question Type:</label>
                    <select name="questions[${questionCount}][type]" class="question-type">
                        <option value="multiple_choice">Multiple Choice</option>
                        <option value="single_choice">Single Choice</option>
                        <option value="fill_in_the_blanks">Fill in the Blanks</option>
                        <option value="essay">Essay</option>
                        <option value="matching">Matching</option>
                        <option value="drag_and_drop">Drag and Drop</option>
                        <option value="true_false">True/False</option>
                        <option value="short_answer">Short Answer</option>
                    </select>
                </div>
                <div class="options-container" data-question="${questionCount}">
                    <!-- Options will be added here for multiple choice questions -->
                </div>
                <button type="button" class="add-option">Add Option</button>
                <div class="form-group">
                    <label>Points:</label>
                    <input type="number" name="questions[${questionCount}][points]" min="1" value="1">
                </div>
            `;
            
            container.appendChild(questionDiv);
        });
    </script>
</body>
</html>
