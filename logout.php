<?php
session_start();

// Check session and redirect accordingly
if (isset($_SESSION['department_id'])) {
    session_destroy();
    header('Location: login.php');
    exit;
} elseif (isset($_SESSION['employee_id'])) {
    session_destroy();
    header('Location: index.php');
    exit;
} else {
    // Default redirection if no specific session exists
    session_destroy();
    header('Location: index.php');
    exit;
}
?>
