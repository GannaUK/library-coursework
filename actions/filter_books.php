<?php
require_once '../includes/db.php';

$genre = $_GET['genre'] ?? '';
$titleFragment = $_GET['title'] ?? '';
$authorFragment = $_GET['author'] ?? '';

$sql = "SELECT * FROM books WHERE 1=1";
$params = [];

if (!empty($genre)) {
    $sql .= " AND genre LIKE :genre";
    $params['genre'] = '%' . $genre . '%';
}

if (!empty($titleFragment)) {
    $sql .= " AND title LIKE :title";
    $params['title'] = '%' . $titleFragment . '%';
}

if (!empty($authorFragment)) {
    $sql .= " AND author LIKE :author";
    $params['author'] = '%' . $authorFragment . '%';
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$books = $stmt->fetchAll();

header('Content-Type: application/json');
echo json_encode($books);
