<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$action = $_SERVER['REQUEST_METHOD'];

try {
    if ($action === 'POST') {
        // create book
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
        // edit book
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
        
        $id = (int)($data['id'] ?? 0);

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM book_movements WHERE book_id = :id");
        $stmt->execute(['id' => $id]);
        $hasBooks = $stmt->fetchColumn();

        if ($hasBooks > 0) {
            http_response_code(409);
            echo json_encode(['success' => false, 'message' => 'Cannot delete book with history data.']);
            exit;
        }

        $stmt = $pdo->prepare("DELETE FROM books WHERE id = :id");
        $stmt->execute(['id' => $id]);

        echo json_encode(['success' => true]);
    } elseif ($action === 'GET') {
        
        $stmt = $pdo->query("
            SELECT 
            b.id,
            b.title,
            b.genre,
            b.author,
            b.description,
            b.max_days,
            COALESCE(SUM(m.quantity), 0) AS available
            FROM books b
            LEFT JOIN book_movements m ON b.id = m.book_id
            GROUP BY b.id, b.title, b.genre, b.author, b.description, b.max_days
            ORDER BY b.id ASC
        ");

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
