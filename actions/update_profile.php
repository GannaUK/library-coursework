<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../includes/db.php';

$is_admin = $_SESSION['is_admin'] ?? false;
$current_session_user = $_SESSION['username'];

// Универсальное получение входных данных: JSON или обычный POST
$input = [];

$contentType = $_SERVER["CONTENT_TYPE"] ?? '';
if (strpos($contentType, 'application/json') !== false) {
    $input = json_decode(file_get_contents("php://input"), true) ?? [];
} else {
    $input = $_POST;
}

// Определяем редактируемого пользователя
$username = $input['username'] ?? $current_session_user;

if ($username !== $current_session_user && !$is_admin) {
    // Нельзя редактировать чужие данные, если ты не админ
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Получаем текущие значения
$stmt = $pdo->prepare("SELECT email, dob FROM db_users WHERE username = :username");
$stmt->execute(['username' => $username]);
$current = $stmt->fetch();

$email = trim($input['email'] ?? '');
$dob = trim($input['dob'] ?? '');
$password = $input['password'] ?? '';
$is_admin_POST = isset($input['isAdmin']) && $input['isAdmin'] ? 1 : 0;

// Обновления
try {
    if (!empty($email) && $email !== $current['email']) {
        $stmt = $pdo->prepare("UPDATE db_users SET email = :email WHERE username = :username");
        $stmt->execute(['email' => $email, 'username' => $username]);
    }

    if (!empty($dob) && $dob !== $current['dob']) {
        $stmt = $pdo->prepare("UPDATE db_users SET dob = :dob WHERE username = :username");
        $stmt->execute(['dob' => $dob, 'username' => $username]);
    }

    if (!empty($password)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE db_users SET password = :password WHERE username = :username");
        $stmt->execute(['password' => $hashed, 'username' => $username]);
    }

    // Только если админ редактирует или сам себя — обновим is_admin
    if ($is_admin || $username === $current_session_user) {
        if (array_key_exists('isAdmin', $input)) {
            $stmt = $pdo->prepare("UPDATE db_users SET is_admin = :is_admin WHERE username = :username");
            $stmt->execute(['is_admin' => $is_admin_POST, 'username' => $username]);
        }
    }

    // Возвращаем JSON, если был JSON-запрос
    if (strpos($contentType, 'application/json') !== false) {
        echo json_encode(['success' => true, 'message' => 'User updated successfully']);
        exit;
    }

    // Иначе редирект
    $redirectPage = ($username === $current_session_user) ? 'user_dashboard.php' : 'admin_dashboard.php';
    header("Location: ../$redirectPage");
    exit;
} catch (PDOException $e) {
    if (strpos($contentType, 'application/json') !== false) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    } else {
        die("Database error: " . $e->getMessage());
    }
}
