<?php
require_once '../includes/db.php';

$genre = trim($_GET['genre'] ?? '');
$title = trim($_GET['title'] ?? '');
$author = trim($_GET['author'] ?? '');

$sql = "
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
    WHERE 1=1
";

$params = [];

if ($genre !== '') {
    $sql .= " AND b.genre LIKE :genre";
    $params['genre'] = '%' . $genre . '%';
}

if ($author !== '') {
    $sql .= " AND b.author LIKE :author";
    $params['author'] = '%' . $author . '%';
}

if ($title !== '') {
    $sql .= " AND b.title LIKE :title";
    $params['title'] = '%' . $title . '%';
}

$sql .= "
    GROUP BY b.id, b.title, b.genre, b.author, b.description, b.max_days
    ORDER BY b.id ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$books = $stmt->fetchAll();

header('Content-Type: application/json');
echo json_encode($books);
