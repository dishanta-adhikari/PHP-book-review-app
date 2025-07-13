<?php
require_once __DIR__ . "/../Components/layout.php";

use App\Middleware\SessionMiddleware;
use App\Controllers\BookController;
use App\Helpers\Flash;

SessionMiddleware::validateLoggedInSession('author');

$bookController = new BookController($conn);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $bookController->create($_POST, $_FILES);
}
?>

<?php Flash::render();  ?>

<div class="container-sm my-5">
    <div class="card shadow-sm p-4">
        <h2 class="mb-4 text-center text-md-start">Add New Book | Author</h2>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']) ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php elseif (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']) ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="needs-validation">
            <div class="mb-3">
                <label class="form-label">Book Name</label>
                <input type="text" name="name" class="form-control" required />
            </div>

            <div class="mb-3">
                <label class="form-label">Volume</label>
                <input type="number" name="vol" class="form-control" required />
            </div>

            <div class="mb-3">
                <label class="form-label">Cover Image (JPG/PNG)</label>
                <input type="file" name="cover" class="form-control" accept="image/*" required />
            </div>

            <div class="mb-3">
                <label class="form-label">Book PDF</label>
                <input type="file" name="pdf" class="form-control" accept="application/pdf" required />
            </div>

            <div class="d-flex justify-content-between flex-column flex-md-row gap-3 mt-4">
                <button class="btn btn-dark">Create</button>
                <a class="float-end icon-link icon-link-hover text-decoration-none" href="<?= APP_URL; ?>/src/views/author/dashboard">
                    <svg xmlns="http://www.w3.org/2000/svg" class="bi" viewBox="0 0 16 16" width="16" height="16" aria-hidden="true">
                        <path d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z" />
                    </svg>
                    back to dashboard
                </a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . "/../Components/footer.php"; ?>