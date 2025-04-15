<?php
require_once '../includes/db.php';
session_start();


$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$dob = $_POST['dob'] ?? '';


if (empty($username) || empty($email) || empty($dob)) {
    echo "Please fill in all three fields.";
    exit;
}

$stmt = $pdo->prepare("
    SELECT id FROM db_users 
    WHERE username = :username AND email = :email AND dob = :dob
");

$stmt->execute([
    'username' => $username,
    'email' => $email,
    'dob' => $dob,
]);

$user = $stmt->fetch();

if ($user) {

    $_SESSION['recover_user_id'] = $user['id'];

?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <title>Set new password</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>

    <body class="bg-light">

        <div class="container d-flex justify-content-center align-items-center vh-100">
            <div class="card p-4 shadow" style="width: 100%; max-width: 400px;">
                <h5 class="text-center mb-3">Identity confirmed</h5>
                <p class="text-center mb-4">Set your new password below:</p>

                <form method="POST" action="reset_password.php">
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New password</label>
                        <input type="password" name="new_password" class="form-control" id="new_password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Reset Password</button>
                </form>
            </div>
        </div>

    </body>

    </html>
<?php
} else {
    echo "No user found with the provided details.";
}
