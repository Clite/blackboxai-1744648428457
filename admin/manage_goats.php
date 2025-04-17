<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
redirectIfNotLoggedIn();

if (!isAdmin()) {
    header('Location: ../index.php');
    exit();
}

// Handle adding a new goat
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_goat'])) {
    $name = $_POST['name'];
    $breed = $_POST['breed'];
    $age = $_POST['age'];
    $health_status = $_POST['health_status'];

    $stmt = $pdo->prepare("INSERT INTO goats (name, breed, age, health_status) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $breed, $age, $health_status]);
    header('Location: manage_goats.php');
    exit();
}

// Fetch all goats
$goats = $pdo->query("SELECT * FROM goats")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Goats</title>
</head>
<body>
    <h1>Manage Goats</h1>
    <form method="POST">
        <input type="text" name="name" placeholder="Goat Name" required>
        <input type="text" name="breed" placeholder="Breed">
        <input type="number" name="age" placeholder="Age">
        <input type="text" name="health_status" placeholder="Health Status">
        <button type="submit" name="add_goat">Add Goat</button>
    </form>

    <h2>Existing Goats</h2>
    <ul>
        <?php foreach ($goats as $goat): ?>
            <li><?php echo htmlspecialchars($goat['name']); ?> - <?php echo htmlspecialchars($goat['breed']); ?></li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
