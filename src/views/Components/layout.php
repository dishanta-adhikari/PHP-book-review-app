<?php
require_once __DIR__ . "/../../_init_.php";
?>

<!doctype html>
<html lang="en">

<head>
    <?php if (isset($_SESSION['user_name'])) :  ?>
        <title> <?= $_SESSION['user_name'] . " | Dashboard"  ?></title>
    <?php else :  ?>
        <title>BRS | Books Review System</title>
    <?php endif; ?>

    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet" crossorigin="anonymous" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

</head>


<body class="d-flex flex-column vh-100 ">
    <header class="bg-dark text-white mb-4 sticky-top">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark py-3">
            <div class="container-fluid px-3 px-md-5">

                <?php if (isset($_SESSION['user_name'])) :  ?>
                    <a class="navbar-brand mb-0 fs-3 fw-bold" href="<?= APP_URL; ?>">
                        <?= $_SESSION['user_name']  ?>
                    </a>
                <?php else :  ?>
                    <a class="navbar-brand mb-0 fs-3 fw-bold" href="<?= APP_URL; ?>">
                        Books Review System
                    </a>
                <?php endif; ?>

                <!-- Toggler for small screens -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Collapsible Menu -->
                <div class="collapse navbar-collapse justify-content-lg-end mt-3 mt-lg-0" id="navbarNav">
                    <div class="d-flex flex-column flex-lg-row align-items-lg-center gap-2">

                        <?php if (isset($_SESSION['author_id'])): ?>
                            <a href="<?= APP_URL ?>/src/views/book/create" class="btn btn-primary btn-sm">New Book</a>
                            <a href="<?= AUTH_URL ?>/logout" class="btn btn-danger btn-sm">Log Out</a>
                        <?php elseif (isset($_SESSION['user_id'])): ?>
                            <a href="<?= AUTH_URL ?>/logout" class="btn btn-danger btn-sm">Log Out</a>
                        <?php else: ?>
                            <a href="<?= AUTH_URL ?>/user/login" class="btn btn-light btn-sm">Log In</a>
                            <a href="<?= AUTH_URL ?>/user/register" class="btn btn-outline-light btn-sm">Register</a>
                        <?php endif; ?>

                    </div>
                </div>

            </div>
        </nav>
    </header>


    <!-- Your page content goes here -->

</body>

</html>