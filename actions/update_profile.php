<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../includes/db.php';

$username = $_SESSION['username'];

$stmt = $pdo->prepare("SELECT email, dob FROM db_users WHERE username = :username");
$stmt->execute(['username' => $username]);
$current = $stmt->fetch();

$email = trim($_POST['email'] ?? '');
$dob = trim($_POST['dob'] ?? '');
$password = $_POST['password'] ?? '';

// Update only if the value is not empty and differs from the current one
if (!empty($email) && $email !== $current['email']) {
    $stmt = $pdo->prepare("UPDATE db_users SET email = :email WHERE username = :username");
    $stmt->execute([
        'email' => $email,
        'username' => $username
    ]);
}


if (!empty($dob) && $dob !== $current['dob']) {
    $stmt = $pdo->prepare("UPDATE db_users SET dob = :dob WHERE username = :username");
    $stmt->execute([
        'dob' => $dob,
        'username' => $username
    ]);
}

if (!empty($password)) {
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE db_users SET password = :password WHERE username = :username");
    $stmt->execute([
        'password' => $hashed,
        'username' => $username
    ]);
}

$redirectPage = 'user_dashboard.php'; // Default redirect page
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $redirectPage = 'admin_dashboard.php';
}

header("Location: ../$redirectPage#settings");
exit;
