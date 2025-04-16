<?php
require_once '../includes/db.php';

$sql = "
    SELECT b.title, ABS(SUM(m.quantity)) AS total_borrowed
    FROM book_movements m
    JOIN books b ON m.book_id = b.id
    WHERE m.quantity < 0
    GROUP BY b.id, b.title
    ORDER BY total_borrowed DESC
    LIMIT 5
";

$stmt = $pdo->query($sql);
$data = $stmt->fetchAll();

header('Content-Type: application/json');
echo json_encode($data);
