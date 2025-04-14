<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isTeacher()) {
    header('Location: ../login.php');
    exit();
}

$exams = $pdo->query("SELECT id, title FROM exams WHERE created_by = " . intval($_SESSION['user_id']))->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['export_exam_id'])) {
        // Export questions as CSV
        $exam_id = intval($_POST['export_exam_id']);
        $stmt = $pdo->prepare("SELECT * FROM questions WHERE exam_id = ?");
        $stmt->execute([$exam_id]);
        $questions = $stmt->fetchAll();

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="exam_' . $exam_id . '_questions.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['question_text', 'question_type', 'points']);

        foreach ($questions as $question) {
            fputcsv($output, [$question['question_text'], $question['question_type'], $question['points']]);
        }
        fclose($output);
        exit();
    }

    if (isset($_POST['import_exam_id']) && isset($_FILES['import_file'])) {
        $exam_id = intval($_POST['import_exam_id']);
        $file = $_FILES['import_file']['tmp_name'];

        if (($handle = fopen($file, 'r')) !== false) {
            $header = fgetcsv($handle);
            $count = 0;
            while (($data = fgetcsv($handle)) !== false) {
                $question_text = $data[0];
                $question_type = $data[1];
                $points = intval($data[2]);

                $stmt = $pdo->prepare("INSERT INTO questions (exam_id, question_text, question_type, points) VALUES (?, ?, ?, ?)");
                $stmt->execute([$exam_id, $question_text, $question_type, $points]);
                $count++;
            }
            fclose($handle);
            $_SESSION['success'] = "$count questions imported successfully.";
        } else {
            $_SESSION['error'] = "Failed to open the uploaded file.";
        }
        header('Location: import_export_questions.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Import/Export Questions - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../assets/style.css" />
</head>
<body>
    <div class="container">
        <h1>Import and Export Exam Questions</h1>

        <?php if (isset($_SESSION['success'])): ?>
            <p class="success"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></p>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <p class="error"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
        <?php endif; ?>

        <h2>Export Questions</h2>
        <form method="POST">
            <label for="export_exam_id">Select Exam:</label>
            <select name="export_exam_id" id="export_exam_id" required>
                <option value="">-- Select Exam --</option>
                <?php foreach ($exams as $exam): ?>
                    <option value="<?php echo $exam['id']; ?>"><?php echo htmlspecialchars($exam['title']); ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Export as CSV</button>
        </form>

        <h2>Import Questions</h2>
        <form method="POST" enctype="multipart/form-data">
            <label for="import_exam_id">Select Exam:</label>
            <select name="import_exam_id" id="import_exam_id" required>
                <option value="">-- Select Exam --</option>
                <?php foreach ($exams as $exam): ?>
                    <option value="<?php echo $exam['id']; ?>"><?php echo htmlspecialchars($exam['title']); ?></option>
                <?php endforeach; ?>
            </select>
            <label for="import_file">CSV File:</label>
            <input type="file" name="import_file" id="import_file" accept=".csv" required />
            <button type="submit">Import Questions</button>
        </form>

        <p><a href="index.php">Back to Teacher Dashboard</a></p>
    </div>
</body>
</html>
