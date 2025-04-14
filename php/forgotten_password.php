<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Recover password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow" style="width: 100%; max-width: 400px;">
            <b class="text-center mb-4">To recover your password, provide at least 3 (three) known details</b>
            <form method="POST" action="register.php">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" id="username">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" id="password">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" name="email" class="form-control" id="email">
                </div>
                <div class="mb-3">
                    <label for="dob" class="form-label">Date of birthday</label>
                    <input type="date" name="dob" class="form-control" id="dob">
                </div>

                <button type="submit" class="btn btn-outline-primary btn-lg w-100">Recover password</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>