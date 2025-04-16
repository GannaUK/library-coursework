<?php
session_start();  // Start the session

// If the session does not exist, redirect to the login page immediately
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../includes/db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$action = $_SERVER['REQUEST_METHOD'];
$user_id = (int)$_SESSION['user_id'];
try {
    if ($action === 'POST') {
        // Добавление новой записи о движении книги
        $book_id = (int)($data['book_id'] ?? 0);

        $quantity = (int)($data['quantity'] ?? 0);
        $movement_date = $data['movement_date'] ?? null;

        if ($book_id === 0 || $quantity === 0) {
            throw new Exception("Book ID and quantity are required.");
        }

        $stmt = $pdo->prepare("
            INSERT INTO book_movements (book_id, user_id, quantity, movement_date)
            VALUES (:book_id, :user_id, :quantity, COALESCE(:movement_date, (CURRENT_DATE)))
        ");
        $stmt->execute([
            'book_id' => $book_id,
            'user_id' => $user_id,
            'quantity' => $quantity,
            'movement_date' => $movement_date,
        ]);

        echo json_encode(['success' => true]);
    } elseif ($action === 'GET') {
        
        if (isset($_GET['check_active']) && $_GET['check_active'] == '1') {
            if (!isset($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
                throw new Exception("User ID is required for checking.");
            }

            $stmt = $pdo->prepare("
                SELECT COUNT(*) AS active_loans
                FROM (
                    SELECT book_id
                    FROM book_movements
                    WHERE user_id = :user_id
                    GROUP BY book_id
                    HAVING SUM(quantity) < 0
                ) AS active
            ");
            $stmt->execute(['user_id' => (int)$_GET['user_id']]);
            $count = $stmt->fetchColumn();

            echo json_encode(['success' => true, 'active_loans' => (int)$count]);
            exit;
        }

        // Получение всех движений книг с возможной фильтрацией
        $filters = [];
        $params = [];

        if (isset($_GET['user_id']) && $_GET['user_id'] === 'me') {
            $filters[] = 'bm.user_id = :user_id';
            $params['user_id'] = $user_id; // из $_SESSION['user_id']
        }

        if (isset($_GET['user_id']) && is_numeric($_GET['user_id'])) {
            $filters[] = 'bm.user_id = :user_id';
            $params['user_id'] = (int)$_GET['user_id'];
        }

        if (isset($_GET['book_id']) && is_numeric($_GET['book_id'])) {
            $filters[] = 'bm.book_id = :book_id';
            $params['book_id'] = (int)$_GET['book_id'];
        }

        if (isset($_GET['date'])) {
            $filters[] = 'DATE(bm.movement_date) = :movement_date';
            $params['movement_date'] = $_GET['date']; // Ожидается в формате YYYY-MM-DD
        }

        $whereClause = '';
        if (!empty($filters)) {
            $whereClause = 'WHERE ' . implode(' AND ', $filters);
        }

        $stmt = $pdo->prepare("
            SELECT 
                b.title,
                b.author,
                -SUM(bm.quantity) AS quantity,  -- показываем как положительное число
                MIN(bm.movement_date) AS movement_date,
                DATE_ADD(MIN(bm.movement_date), INTERVAL b.max_days DAY) AS expected_return_date
            FROM book_movements bm
            INNER JOIN books b ON bm.book_id = b.id
            WHERE bm.user_id = :user_id
                                    
            $whereClause
            GROUP BY bm.book_id, b.title, b.author, b.max_days
                HAVING SUM(bm.quantity) < 0
                ORDER BY movement_date DESC 
            ");
        $stmt->execute($params);
        $movements = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'movements' => $movements]);
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
