<?php
require_once '../includes/db.php';

$sql = "
SELECT
b.genre,
ABS(SUM(CASE WHEN m.quantity < 0 THEN m.quantity ELSE 0 END)) AS total_borrowed
    FROM books b
    LEFT JOIN book_movements m ON b.id=m.book_id
    GROUP BY b.genre
    ORDER BY total_borrowed DESC 
    LIMIT 5";

$stmt = $pdo->query($sql);
$genreStats = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($genreStats);