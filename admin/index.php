<?php
// Admin dashboard
require_once '../includes/config.php';
require_once '../includes/auth.php';
redirectIfNotLoggedIn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo APP_NAME; ?></title>
</head>
<body>
    <h1>Welcome to the Admin Dashboard</h1>
    <p><a href="../logout.php">Logout</a></p>
</body>
</html>
