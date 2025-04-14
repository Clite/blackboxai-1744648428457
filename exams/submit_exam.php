<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isStudent()) {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $exam_id = $_POST['exam_id'];
    $user_id = $_SESSION['user_id'];
    
    // Insert exam attempt
    $stmt = $pdo->prepare("INSERT INTO exam_attempts (exam_id, user_id) VALUES (?, ?)");
    $stmt->execute([$exam_id, $user_id]);
    $attempt_id = $pdo->lastInsertId();
    
    // Process answers
    foreach ($_POST['questions'] as $question) {
        $question_id = $question['id'];
        $answer = $question['answer'];
        
        // Insert answer into the database
        $stmt = $pdo->prepare("INSERT INTO answers (attempt_id, question_id, answer_text) VALUES (?, ?, ?)");
        $stmt->execute([$attempt_id, $question_id, $answer]);
    }
    
    // Redirect to results page or show success message
    $_SESSION['success'] = "Exam submitted successfully!";
    header('Location: index.php'); // Change to results page if implemented
    exit();
}
?>
