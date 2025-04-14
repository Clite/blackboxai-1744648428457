<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Redirect based on user role
if (isset($_SESSION['user_role'])) {
    switch($_SESSION['user_role']) {
        case 'admin':
            header('Location: admin/');
            break;
        case 'teacher':
            header('Location: teacher/');
            break;
        case 'student':
            header('Location: exams/');
            break;
    }
} else {
    header('Location: login.php');
}
