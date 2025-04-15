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
                <div class="card shadow-sm p-4">
                    <h5 class="card-title mb-3">Manage Users</h5>
                    <p>Here will be the user management table or controls.</p>
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
</body>

</html>