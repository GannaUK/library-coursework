<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../includes/db.php';

$is_admin = $_SESSION['is_admin'] ?? false;
$current_session_user = $_SESSION['username'];

// Разрешаем редактировать чужие данные только админам
$username = $_POST['username'] ?? $current_session_user;
if ($username !== $current_session_user && !$is_admin) {
    // Пользователь пытается редактировать чужой профиль
    header('Location: ../login.php');
    exit;
}

// Получаем текущие значения
$stmt = $pdo->prepare("SELECT email, dob FROM db_users WHERE username = :username");
$stmt->execute(['username' => $username]);
$current = $stmt->fetch();

$email = trim($_POST['email'] ?? '');
$dob = trim($_POST['dob'] ?? '');
$password = $_POST['password'] ?? '';

$is_admin_POST = isset($_POST['edit_is_admin']) && $_POST['edit_is_admin'] === 'on';

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
if (isset($_POST['edit_is_admin'])) {
    
    $stmt = $pdo->prepare("UPDATE db_users SET is_admin = :is_admin WHERE username = :username");
    $stmt->execute([
        'is_admin' => $is_admin_POST,
        'username' => $username
    ]);
}

// Если админ — вернём в админку, если обычный — в личный кабинет
$redirectPage = ($username === $current_session_user) ? 'user_dashboard.php' : 'admin_dashboard.php';
header("Location: ../$redirectPage");
exit;
