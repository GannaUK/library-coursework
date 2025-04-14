<?php
$prefilledUsername = $_GET['username'] ?? '';

session_start(); 

// Check if the user is already logged in, redirect to the appropriate dashboard
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {

    // Redirect to the dashboard based on the role
    if ($_SESSION['role'] === 'admin' && $_SESSION['is_admin'] == 1) {
        // If admin, redirect to the admin dashboard
        header('Location: admin_dashboard.php');
    } else {
        // If user, redirect to the user dashboard
        header('Location: user_dashboard.php');
    }
   
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    require_once 'includes/db.php';

    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'] ?? 'user';


    $stmt = $pdo->prepare("SELECT id, password, is_admin FROM db_users WHERE username = :username");
    $stmt->execute(['username' => $username]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    // Check if the user exists and the password is correct
    if ($user && password_verify($password, $user['password'])) {
        // If the user selected admin, verify their admin rights
        if ($role === 'admin' && $user['is_admin'] == 0) {
            // If the user is not an admin, show an error
            $error_message = "You do not have admin rights.";
        } else {
            // If the user has admin rights or selected the user role
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role; // Save the role in the session
            $_SESSION['is_admin'] = $user['is_admin'];

            // Redirect to the appropriate dashboard based on the role
            if ($role === 'admin' && $user['is_admin'] == 1) {
                // If admin, redirect to the admin dashboard
                header('Location: admin_dashboard.php');
            } else {
                // If user, redirect to the user dashboard
                header('Location: user_dashboard.php');
            }
            exit;
        }
    } else {
        // If the username or password is incorrect
        $error_message = "Invalid username or password!";
    }
}
?>

<?php if (isset($error_message)): ?>
    <div class="alert alert-danger" role="alert">
        <?php echo $error_message; ?>
    </div>
<?php endif; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow" style="width: 100%; max-width: 400px;">
            <h2 class="text-center mb-4">Login</h2>
            <form method="POST" action="login.php">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($prefilledUsername); ?>" class="form-control" id="username" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" id="password" required>
                </div>
                <div class="mb-3">
                    <select class="form-select form-select-sm" aria-label="Small select" name="role" id="role">
                        <option selected="">Select role</option>
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-outline-primary btn-lg w-100">Log In</button>
                <a href="forgotten_password.php" class="btn btn-link">Forgot password</a>

            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>