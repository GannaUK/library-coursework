<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['recover_user_id'])) {
    header('Location: login.php');
    exit;
}

$new_password = $_POST['new_password'] ?? '';
if (strlen($new_password) < 1) {
    echo "Password too short";
    exit;
}


$stmt = $pdo->prepare("UPDATE db_users SET password = :password WHERE id = :id");
$stmt->execute([
    'password' => password_hash($new_password, PASSWORD_DEFAULT),
    'id' => $_SESSION['recover_user_id']
]);

unset($_SESSION['recover_user_id']); 
header('Location: ../login.php?reset=success');
exit;
