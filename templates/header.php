<?php
session_start();
include 'includes/db.php';
require 'includes/user.php';

if (!isset($_SESSION["username"])) {
    header('location:../login.php');
} else {
    $db = new Database();
    $pdo = $db->getConnection();

    $name = $_SESSION["username"];
    $user1 = new User($pdo);
    $user = $user1->getUser($name);
    ?>
    <!-- templates/header.php -->
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Smart Recruitment System</title>
        <link href="library/bootstrap/css/bootstrap.min.css" rel="stylesheet"
            integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

        <script src="library/bootstrap/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>
        <style>
            .navbar-custom {
                background-color: #563d7c;
            }

            .navbar-brand {
                font-weight: bold;
                color: #fff !important;
            }

            .nav-link {
                color: #ddd !important;
            }

            .nav-link:hover {
                color: #fff !important;
            }

            .dropdown-menu {
                background-color: #563d7c;
            }

            .dropdown-item {
                color: #ddd !important;
            }

            .dropdown-item:hover {
                color: #fff !important;
                background-color: transparent;
            }
        </style>
    </head>

    <body>
        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-custom">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php">Job Seek.Com</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <form class="d-flex me-auto" role="search" action="index.php">
                        <input type="text" name="search" class="form-control me-2" placeholder="Search Jobs" aria-label="Search">
                        <button class="btn btn-outline-success" type="submit">Search</button>
                    </form>
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="index.php">Home</a>
                        </li>
                        <?php if ($user['role'] == 'employer') { ?>
                            <li class="nav-item">
                                <a class="nav-link" href="dash_board.php">Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="createjob.php">Create Job</a>
                            </li>
                        <?php } ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="profile.php" role="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <?php echo $name; ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="company_detail.php?company=<?= urlencode($user['id']) ?>">Profile</a></li>
                                <li><a class="dropdown-item" href="models/logout_model.php">Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>

            </div>
        </nav>

        <?php
}
?>
    </body>
    </html>