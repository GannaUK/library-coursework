<?php
session_start();  // Start the session

// If the session does not exist, redirect to the login page immediately
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

session_unset();  // Remove all session variables
session_destroy();  // Destroy the session
header('Location: ../index.php');  // Redirect to the login page
exit;
