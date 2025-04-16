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

    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Welcome, <?= htmlspecialchars($username) ?>!</h1>
            <a href="actions/logout.php" class="btn btn-outline-secondary btn-sm">Logout</a>
        </div>

        <ul class="nav nav-tabs mb-3" id="adminTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab">Users Management</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="books-tab" data-bs-toggle="tab" data-bs-target="#books" type="button" role="tab">Books List</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="book-arrivals-tab" data-bs-toggle="tab" data-bs-target="#book-arrivals" type="button" role="tab">Books Management</button>
            </li>

            <li class="nav-item" role="presentation">
                <button class="nav-link" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings" type="button" role="tab">Settings</button>
            </li>
        </ul>

        <div class="tab-content" id="adminTabContent">

            <!-- Users Management Tab -->
            <div class="tab-pane fade show active" id="users" role="tabpanel">
                <div class="row g-4">
                    <!-- Таблица пользователей -->
                    <div class="col-md-8">
                        <div class="card shadow-sm p-3">
                            <h5 class="card-title mb-3">All Users</h5>
                            <button id="show-create-form" class="btn btn-success btn-sm mb-3">+ New User</button>

                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Is Admin</th>
                                        <th>Email</th>
                                        <th>Date of Birth</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $pdo->query("SELECT * FROM db_users ORDER BY id ASC");
                                    while ($row = $stmt->fetch()) {
                                        $id = htmlspecialchars($row['id']);
                                        $username = htmlspecialchars($row['username']);
                                        $email = htmlspecialchars($row['email']);
                                        $dob = htmlspecialchars($row['dob']);
                                        $isAdmin = $row['is_admin'] ? 'Yes' : 'No';
                                        $isAdminVal = (int)$row['is_admin'];

                                        echo "<tr>
                                            <td>{$id}</td>
                                            <td>{$username}</td>
                                            <td>{$isAdmin}</td>
                                            <td>{$email}</td>
                                            <td>{$dob}</td>
                                            <td>
                                                <div class='btn-group' role='group' aria-label='Basic button group'>
                                                <button class='btn btn-sm btn-outline-primary me-1 edit-user' 
                                                    data-id='{$id}'
                                                    data-username='{$username}'
                                                    data-is_admin='{$isAdminVal}'
                                                    data-email='{$email}'
                                                    data-dob='{$dob}'
                                                >Edit</button>
                                                <a href='#' class='btn btn-sm btn-outline-danger btn-delete-user' data-id='{$id}'>Delete</a>
                                            </div>
                                                </td>
                                        </tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Форма редактирования -->
                    <div class="col-md-4">
                        <div id="edit-form-container" class="card shadow-sm p-3 d-none" style="background-color: #f8f9fa;">
                            <h5 class="card-title mb-3">Edit User</h5>
                            <form method="POST" action="actions/update_profile.php">
                                <input type="hidden" name="id" id="edit-id" />
                                <div class="mb-2">
                                    <label for="edit-username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="edit-username" name="username" required />
                                </div>
                                <div class="mb-2">
                                    <label for="edit-email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="edit-email" name="email" />
                                </div>
                                <div class="mb-2">
                                    <label for="edit-dob" class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control" id="edit-dob" name="dob" />
                                </div>
                                <input type="hidden" name="edit_is_admin" value="0" />
                                <div class="form-check mb-3">
                                    <input type="checkbox" class="form-check-input" id="edit_is_admin_cb" name="edit_is_admin" value="1" />
                                    <label for="edit_is_admin_cb" class="form-check-label">Is Admin</label>
                                </div>
                                <button type="submit" name="action" value="edit" class="btn btn-primary w-100">Edit user</button>
                            </form>
                        </div>

                        <!-- Форма создания -->
                        <div id="create-form-container" class="card shadow-sm p-3 d-none" style="background-color: #f8f9fa;">
                            <h5 class="card-title mb-3">Create New User</h5>
                            <form method="POST" action="actions/register.php">
                                <div class="mb-2">
                                    <label class="form-label">Username</label>
                                    <input type="text" class="form-control" name="username" required />
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Password</label>
                                    <input type="password" class="form-control" name="password" required />
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" />
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control" name="dob" />
                                </div>
                                <input type="hidden" name="edit_is_admin" value="0" />
                                <div class="form-check mb-3">
                                    <input type="checkbox" class="form-check-input" id="create_is_admin_cb" name="edit_is_admin" value="1" />
                                    <label for="create_is_admin_cb" class="form-check-label">Is Admin</label>
                                </div>
                                <button type="submit" name="action" value="create" class="btn btn-success w-100">Create User</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Books Management Tab -->
            <div class="tab-pane fade" id="books" role="tabpanel">
                <div class="row g-4">

                    <!-- Таблица книг -->
                    <div class="col-md-8">
                        <div class="card shadow-sm p-3">
                            <h5 class="card-title mb-3">All Books </h5>
                            <button id="show-create-book-form" class="btn btn-success btn-sm mb-3">+ Add New Book</button>

                            <!-- Форма фильтрации книг -->
                            <form id="book-filter-form" class="row g-2 mb-3">
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="title" placeholder="Book Title" />
                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="author" placeholder="Author" />
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select" name="genre">
                                        <option value="">All Genres</option>
                                        <option value="Fiction">Fiction</option>
                                        <option value="History">History</option>
                                        <option value="Science">Science</option>
                                    </select>
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
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $pdo->query("SELECT * FROM books ORDER BY id ASC");
                                    while ($book = $stmt->fetch()) {
                                        $bookId = htmlspecialchars($book['id']);
                                        $title = htmlspecialchars($book['title']);
                                        $author = htmlspecialchars($book['author']);
                                        $genre = htmlspecialchars($book['genre']);
                                        $description = htmlspecialchars($book['description']);
                                        $days = htmlspecialchars($book['max_days']);

                                        echo "<tr>
                                <td>{$bookId}</td>
                                <td>{$title}</td>
                                <td>{$author}</td>
                                <td>{$genre}</td>
                                <td>{$description}</td>
                                <td>{$days}</td>
                                <td>
                                <div class='btn-group' role='group' aria-label='Basic button group'>
                                    <button class='btn btn-sm btn-outline-primary me-1 edit-book-btn'
                                        data-id='{$bookId}'
                                        data-title='{$title}'
                                        data-author='{$author}'
                                        data-genre='{$genre}'
                                        data-description='{$description}'
                                        data-days='{$days}'
                                    >  Edit     </button>
                                    <button class='btn btn-sm btn-outline-danger delete-book-btn' data-id='{$bookId}'>Delete</button>
                                </div>
                                </td>
                            </tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Форма  создания -->
                    <div class="col-md-4">
                        <div id="book-form-container" class="card shadow-sm p-3 d-none" style="background-color: #f8f9fa;">

                            <div id="create-book-form" class="d-none">
                                <h5 class="card-title mb-3" id="book-form-title">Add Book</h5>
                                <form id="book-create-form">
                                    <input type="hidden" id="book-id" name="id" />
                                    <div class="mb-2">
                                        <label class="form-label">Title</label>
                                        <input type="text" class="form-control" name="title" id="book-title" required />
                                    </div>

                                    <div class="mb-2">
                                        <label for="book-author" class="form-label">Author</label>
                                        <input type="text" class="form-control" id="book-author" name="author" required>
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label">Genre</label>
                                        <input type="text" class="form-control" name="genre" id="book-genre" required />
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Description</label>
                                        <textarea class="form-control" name="description" id="book-description" required></textarea>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Max Days</label>
                                        <input type="number" class="form-control" name="max_days" id="book-days" required min="1" />
                                    </div>

                                    <div class="border border-primary rounded p-3 mb-3 bg-light">
                                        <h6 class="mb-2">Stock Management</h6>
                                        <div class="mb-2">
                                            <label class="form-label">Enter the number of copies: positive to add to the shelf, negative to remove.</label>
                                            <input type="number" class="form-control" name="quantity" id="quantity" min="0" />
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-success w-100" id="book-form-submit">Save Book</button>
                                </form>

                            </div>

                            <!-- Форма редактирования -->
                            <div id="edit-book-form" class="d-none">
                                <h5 class="card-title mb-3">Edit Book</h5>
                                <form id="book-edit-form">
                                    <input type="hidden" name="id" id="edit-book-id" />
                                    <!-- остальные поля, с префиксом edit- -->
                                    <div class="mb-2">
                                        <label class="form-label">Title</label>
                                        <input type="text" class="form-control" name="title" id="edit-book-title" required />
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Author</label>
                                        <input type="text" class="form-control" name="author" id="edit-book-author" required />
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Genre</label>
                                        <input type="text" class="form-control" name="genre" id="edit-book-genre" required />
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Description</label>
                                        <textarea class="form-control" name="description" id="edit-book-description" required></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Max Days</label>
                                        <input type="number" class="form-control" name="max_days" id="edit-book-days" required min="1" />
                                    </div>
                                   
                                    <div class="border border-primary rounded p-3 mb-3 bg-light">
                                        <h6 class="mb-2">Stock Management</h6>
                                        <div class="mb-2">
                                            <label class="form-label">Enter the number of copies: positive to add to the shelf, negative to remove.</label>
                                            <input type="number" class="form-control" name="quantity" id="quantity" min="0" />
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100" id="book-form-submit">Save Changes</button>
                                </form>
                            </div>



                        </div>
                    </div>
                </div>
            </div>


            <!-- Book Arrivals Tab -->
            <div class="tab-pane fade" id="book-arrivals" role="tabpanel">
                <div class="card shadow-sm p-4">
                    <h5 class="card-title mb-3">Book Arrivals</h5>
                    <p>Here we will manage book movement entries (IN).</p>
                </div>
            </div>

            <!-- Settings Tab -->
            <div class="tab-pane fade" id="settings" role="tabpanel">
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
    <script src="js/admin_dashboard.js"></script>
</body>

</html>