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
                <button class="nav-link" id="books-tab" data-bs-toggle="tab" data-bs-target="#books" type="button" role="tab">Books Management</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings" type="button" role="tab">Settings</button>
            </li>
        </ul>

        <div class="tab-content" id="adminTabContent">


            <!-- Users Management Tab -->
            <div class="tab-pane fade show active" id="users" role="tabpanel">
                <!-- <div class="card shadow-sm p-4">
                    <h5 class="card-title mb-3">Manage Users</h5>
                    <p>Here will be the user management table or controls.</p>
                </div> -->
                <div class="row g-4">
                    <!-- Левая часть: Таблица пользователей -->
                    <div class="col-md-8">
                        <div class="card shadow-sm p-3">
                            <h5 class="card-title mb-3">All Users</h5>
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
                                        echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['username']}</td>
                            <td>" . ($row['is_admin'] ? 'Yes' : 'No') . "</td>
                            <td>{$row['email']}</td>
                            <td>{$row['dob']}</td>
                            <td>
                                <button class='btn btn-sm btn-outline-primary me-1 edit-user' 
                                    data-id='{$row['id']}'
                                    data-username='{$row['username']}'
                                    data-is_admin='{$row['is_admin']}'
                                    data-email='{$row['email']}'
                                    data-dob='{$row['dob']}'
                                >Edit</button>
                                <a href='actions/delete_user.php?id={$row['id']}' class='btn btn-sm btn-outline-danger'>Delete</a>
                            </td>
                        </tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Правая часть: Форма редактирования -->
                    <div class="col-md-4">
                        <div class="card shadow-sm p-3" style="background-color: #f8f9fa;">
                            <h5 class="card-title mb-3">Add / Edit User</h5>
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
                                <!-- Скрытое поле для передачи ложного значения -->
                                <input type="hidden" name="edit_is_admin" value="false" />

                                <div class="form-check mb-3">
                                    <input type="checkbox" class="form-check-input" id="edit_is_admin" name="edit_is_admin" />
                                    <label for="edit_is_admin" class="form-check-label">Is Admin</label>
                                </div>
                                <button type="submit" class="btn btn-primary w-100"> Edit user</button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Books Management Tab -->
            <div class="tab-pane fade" id="books" role="tabpanel">
                <div class="card shadow-sm p-4">
                    <h5 class="card-title mb-3">Manage Books</h5>
                    <p>Here will be the books management table or controls.</p>
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