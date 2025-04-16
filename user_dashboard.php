<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}
$username = $_SESSION['username'];

require_once 'includes/db.php';

$stmt = $pdo->prepare("SELECT email, dob FROM db_users WHERE username = :username");
$stmt->execute(['username' => $username]);
$user = $stmt->fetch();
$email = $user['email'] ?? '';
$dob = $user['dob'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body class="bg-light">
    
    <?php if (isset($_SESSION['user_id'])): ?>
        <input type="hidden" id="logged-in-user-id" value="<?= htmlspecialchars($_SESSION['user_id']) ?>">
    <?php endif; ?>

    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Welcome, <?= htmlspecialchars($username) ?>!</h1>
            <a href="actions/logout.php" class="btn btn-outline-secondary btn-sm">Logout</a>
        </div>

        <ul class="nav nav-tabs mb-3" id="adminTab" role="tablist">

            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="books-tab" data-bs-toggle="tab" data-bs-target="#books" type="button" role="tab">Books Management</button>
            </li>

            <li class="nav-item" role="presentation">
                <button class="nav-link" id="book-arrivals-tab" data-bs-toggle="tab" data-bs-target="#book-arrivals" type="button" role="tab">Orders & Returns</button>
            </li>

            <li class="nav-item" role="presentation">
                <button class="nav-link" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings" type="button" role="tab">My Settings</button>
            </li>
        </ul>

        <div class="tab-content" id="adminTabContent">



            <!-- Books Management Tab -->
            <div class="tab-pane fade show active" id="books" role="tabpanel">
                <div class="row g-4">

                    <!-- Таблица книг -->
                    <div class="col-md-8">
                        <div class="card shadow-sm p-3">
                            <h5 class="card-title mb-3">All Books </h5>

                            <!-- Форма фильтрации книг -->
                            <form id="book-filter-form" class="row g-2 mb-3">
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="title" placeholder="Book Title" />
                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="author" placeholder="Author" />
                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="genre" placeholder="Genre" />
                                </div>


                                <!-- Кнопки в одной строке -->
                                <div class="col-md-3">
                                    <div class="row g-2">
                                        <div class='btn-group' role='group' aria-label='Basic button group'>

                                            <button type="submit" class="btn btn-primary w-100">Filter</button>

                                            <button type="button" id="reset-filter" class="btn btn-secondary w-100">Reset</button>

                                        </div>
                                    </div>
                                </div>
                            </form>

                            <table class="table table-bordered table-hover" id="books-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Author</th>
                                        <th>Genre</th>
                                        <th>Description</th>
                                        <th>Max Days</th>
                                        <th>Available</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
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

                                    while ($book = $stmt->fetch()) {
                                        $bookId = htmlspecialchars($book['id']);
                                        $title = htmlspecialchars($book['title']);
                                        $author = htmlspecialchars($book['author']);
                                        $genre = htmlspecialchars($book['genre']);
                                        $description = htmlspecialchars($book['description']);
                                        $days = htmlspecialchars($book['max_days']);
                                        $available = (int) $book['available']; // для сравнения используем как число

                                        echo "<tr>
                                            <td>{$bookId}</td>
                                            <td>{$title}</td>
                                            <td>{$author}</td>
                                            <td>{$genre}</td>
                                            <td>{$description}</td>
                                            <td>{$days}</td>
                                            <td>{$available}</td>
                                            <td>";

                                        if ($available > 0) {
                                            echo "<button class='btn btn-sm btn-primary order-book-btn' data-id='{$bookId}'>Order</button>";
                                        } else {
                                            echo "<span class='text-muted'>Not available</span>";
                                        }

                                        echo "</td>
                                                    </tr>";
                                    }
                                    ?>

                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>



            <!-- Orders & Returns Tab -->
            <div class="tab-pane fade" id="book-arrivals" role="tabpanel">
                <div class="card shadow-sm p-4">
                    <h5 class="card-title mb-3">Orders & Returns</h5>
                    <p>Here we will manage book movement entries (IN).</p>
                    <?php
                    $userId = $_SESSION['user_id']; // предполагаем, что пользователь залогинен

                    // Получаем только активные выдачи: те, по которым ещё нет возврата
                    $stmt = $pdo->prepare("
                                    SELECT 
                                        bm.book_id,
                                        b.title,
                                        b.author,
                                        -SUM(bm.quantity) AS quantity,  -- показываем как положительное число
                                        MIN(bm.movement_date) AS movement_date,
                                        DATE_ADD(MIN(bm.movement_date), INTERVAL b.max_days DAY) AS expected_return_date
                                    FROM book_movements bm
                                    INNER JOIN books b ON bm.book_id = b.id
                                    WHERE bm.user_id = :user_id
                                    GROUP BY bm.book_id, b.title, b.author, b.max_days
                                    HAVING SUM(bm.quantity) < 0
                                    ORDER BY movement_date DESC           
                    ");
                    $stmt->execute(['user_id' => $userId]);
                    $borrowedBooks = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>

                    <table class="table table-bordered table-hover" id="book-activity-table">
                        <thead class="table-light">
                            <tr>
                                <th>Author</th>
                                <th>Title</th>
                                <th>Quantity</th>
                                <th>Borrow Date</th>
                                <th>Expected Return Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <tbody>
                            <?php foreach ($borrowedBooks as $row): ?>
                                <?php
                                $author = htmlspecialchars($row['author']);
                                $title = htmlspecialchars($row['title']);
                                $quantity = abs((int)$row['quantity']); // показываем как положительное
                                $borrowDate = htmlspecialchars($row['movement_date']);
                                $expectedReturn = htmlspecialchars($row['expected_return_date']);
                                $bookId = (int)$row['book_id'];
                                ?>
                                <tr>
                                    <td><?= $author ?></td>
                                    <td><?= $title ?></td>
                                    <td><?= $quantity ?></td>
                                    <td><?= $borrowDate ?></td>
                                    <td><?= $expectedReturn ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-success return-book-btn" data-id="<?= $bookId ?>">Return book</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>

                    </table>




                </div>
            </div>

            <!-- Settings Tab -->
            <div class=" tab-pane fade" id="settings" role="tabpanel">
                <div class="card shadow-sm p-4">
                    <h5 class="card-title mb-3">Edit Your Profile</h5>
                    <form method="POST" action="actions/update_profile.php">
                        <div class="mb-3">
                            <label for="dob" class="form-label">Date of birth</label>
                            <input type="date" class="form-control" id="dob" name="dob" value="<?= htmlspecialchars($dob) ?>" />
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($email) ?>" />
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="password" name="password" />
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>

        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/dashboard.js"></script>
    <script src="js/user_dashboard.js"></script>

</body>

</html>