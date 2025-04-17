<?php
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isLoggedIn() && $_SESSION['user_role'] === 'admin';
}

function isTeacher() {
    return isLoggedIn() && $_SESSION['user_role'] === 'teacher';
}

function isGoatCaretaker() {
    return isLoggedIn() && $_SESSION['user_role'] === 'goat_caretaker';
}

function isVeterinarian() {
    return isLoggedIn() && $_SESSION['user_role'] === 'veterinarian';
}

function isStudent() {
    return isLoggedIn() && $_SESSION['user_role'] === 'student';
}

function redirectIfNotLoggedIn() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}
