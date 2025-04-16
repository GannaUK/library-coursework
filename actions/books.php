<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$action = $_SERVER['REQUEST_METHOD'];

try {
    if ($action === 'POST') {
        // Создание новой книги
        $title = $data['title'] ?? '';
        $author = $data['author'] ?? '';
        $genre = $data['genre'] ?? '';
        $description = $data['description'] ?? '';
        $max_days = (int)($data['max_days'] ?? 14);

        $stmt = $pdo->prepare("INSERT INTO books (title, author, genre, description, max_days) 
                       VALUES (:title, :author, :genre, :description, :max_days)");
        $stmt->execute([
            'title' => $title,
            'author' => $author,
            'genre' => $genre,
            'description' => $description,
            'max_days' => $max_days
        ]);


        echo json_encode(['success' => true]);
    } elseif ($action === 'PUT') {
        // Редактирование существующей книги
        $id = (int)($data['id'] ?? 0);
        $title = $data['title'] ?? '';
        $author = $data['author'] ?? '';
        $genre = $data['genre'] ?? '';
        $description = $data['description'] ?? '';
        $max_days = (int)($data['max_days'] ?? 14);

        $stmt = $pdo->prepare("UPDATE books SET title = :title, author = :author, genre = :genre, description = :description, max_days = :max_days WHERE id = :id");
        $stmt->execute([
            'title' => $title,
            'genre' => $genre,
            'author' => $author,
            'description' => $description,
            'max_days' => $max_days,
            'id' => $id
        ]);

        echo json_encode(['success' => true]);
    } elseif ($action === 'DELETE') {
        // Удаление книги
        $id = (int)($data['id'] ?? 0);

        $stmt = $pdo->prepare("DELETE FROM books WHERE id = :id");
        $stmt->execute(['id' => $id]);

        echo json_encode(['success' => true]);
    } elseif ($action === 'GET') {
        // Получение всех книг
        $stmt = $pdo->query("SELECT * FROM books ORDER BY id ASC");
        $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'books' => $books]);
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
