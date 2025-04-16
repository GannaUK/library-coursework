<?php
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json');

// Только для админов
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access denied.']);
    exit;
}
$input = json_decode(file_get_contents('php://input'), true);

$userId = $input['id'] ?? null;

if (!$userId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'User ID is required.']);
    exit;
}


try {
    // Допустим, у пользователя могут быть книги 
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM book_movements WHERE user_id = :id");
    $stmt->execute(['id' => $userId]);
    $hasBooks = $stmt->fetchColumn();    

    if ($hasBooks > 0 ) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'Cannot delete user with related data.']);
        exit;
    }

    // Всё чисто — удаляем
    $stmt = $pdo->prepare("DELETE FROM db_users WHERE id = :id");
    $stmt->execute(['id' => $userId]);

    echo json_encode(['success' => true, 'message' => 'User deleted successfully.']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}