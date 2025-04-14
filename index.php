<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Web App</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .main-content {
            flex: 1;
        }

        .image-section {
            background: url('images/books.jpg') no-repeat center center;
            background-size: cover;
            min-height: 400px;
        }

        footer {
            background-color: #f8f9fa;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-light bg-light">
        <div class="container-fluid">
            <img src="images/logo.png" alt="Logo" width="30" height="30">
            <a class="navbar-brand small" href="index.html">Home</a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid main-content d-flex">
        <div class="row w-100">
            <!-- Left: Image -->
            <div class="col-md-6 image-section d-none d-md-block"></div>

            <!-- Right: Buttons -->
            <div class="col-md-6 d-flex align-items-center justify-content-center flex-column text-center p-5">
                <h1 class="mb-4">Welcome to the Library</h1>
                <div class="d-grid gap-3 col-6 mx-auto">
                    <a href="login.php" class="btn btn-outline-primary btn-lg">Login</a>
                    <a href="register.php" class="btn btn-outline-primary btn-lg">Register</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center py-3">
        <p>
            <b class="small">Connect with me:</b>
            <img src="images/github_icon.png" alt="GitHub" width="30" height="30">
            <a href="https://github.com/GannaUK" target="_blank">My GitHub</a> |
            <img src="images/linkedin_icon.png" alt="LinkedIn" width="30" height="30">
            <a href="https://www.linkedin.com/in/gkarpycheva/" target="_blank">My LinkedIn</a>
        </p>

    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>