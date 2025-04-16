<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = ($_SESSION['role'] ?? '') === 'admin';
?>

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

        .carousel-inner img {
            object-fit: cover;
        }
        

    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">

            <div class="collapse navbar-collapse justify-content-end">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <?php if ($isLoggedIn): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $isAdmin ? 'admin_dashboard.php' : 'user_dashboard.php' ?>">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-danger" href="logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>


    <!-- Main Content -->
    <div class="container-fluid main-content d-flex">
        <div class="row w-100">

            <!-- Left: Carousel -->
            <div class="col-md-6 d-none d-md-block p-0">
                <div id="libraryCarousel" class="carousel slide h-100" data-bs-ride="carousel">
                    <div class="carousel-inner h-100">
                        <div class="carousel-item active h-100">
                            <img src="images/books.jpg" class="d-block w-100 h-100 object-fit-cover" alt="Books 1">
                        </div>
                        <div class="carousel-item h-100">
                            <img src="images/books2.jpg" class="d-block w-100 h-100 object-fit-cover" alt="Books 2">
                        </div>
                        <div class="carousel-item h-100">
                            <img src="images/books3.jpg" class="d-block w-100 h-100 object-fit-cover" alt="Books 3">
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#libraryCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#libraryCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>


            <!-- Right: Buttons -->
            <div class="col-md-6 d-flex align-items-center justify-content-center flex-column text-center p-5">
                <h1 class="mb-4">Welcome to the Library</h1>
                <div class="d-grid gap-3 col-6 mx-auto">
                    <a href="login.php" class="btn btn-outline-primary btn-lg">Go to Library</a>
                    <a href="register.php" class="btn btn-outline-primary btn-lg">Register</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center py-3">
        <p>
            <b class="small">GANNA KARPYCHEVA (2414562) Intranet Systems Development - CMM007 :</b>
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