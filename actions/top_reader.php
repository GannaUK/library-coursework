<?php
require_once '../includes/db.php';

$sql = "
    SELECT u.username, ABS(SUM(m.quantity)) AS total_books
    FROM book_movements m
    JOIN db_users u ON m.user_id = u.id
    WHERE m.quantity < 0
    GROUP BY u.id, u.username
    ORDER BY total_books DESC 
    
";

$stmt = $pdo->query($sql);
$data = $stmt->fetchAll();

header('Content-Type: application/json');
echo json_encode($data);
