<?php
require_once __DIR__ . "/../Components/layout.php";

use App\Middleware\SessionMiddleware;
use App\Controllers\BookController;
use App\Helpers\Flash;

SessionMiddleware::validateLoggedInSession('author');

$book_id = (int) $_GET['id'];
$author_id = $_SESSION['author_id'];

$bookController = new BookController($conn);
$book = $bookController->getBookById($book_id);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $bookController->update($_POST, $_FILES);
}
?>

<div class="container-sm my-5">
    <div class="card shadow-sm p-4">
        <h2 class="mb-4 text-center text-md-start">Edit Book</h2>

        <?php Flash::render(); ?>

        <form method="post" enctype="multipart/form-data" class="needs-validation">

            <div class="mb-3">
                <label class="form-label">Book Name</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($book['name']); ?>" required />
            </div>

            <div class="mb-3">
                <label class="form-label">Volume</label>
                <input type="number" name="vol" class="form-control" value="<?= htmlspecialchars($book['vol']); ?>" required />
            </div>

            <div class="mb-3">
                <label class="form-label">Current Cover Image</label><br />
                <?php if (!empty($book['img'])): ?>
                    <?php $path = APP_URL . "/public/assets/uploads/"; ?>
                    <img src="<?= $path . htmlspecialchars($book['img']); ?>" alt="Cover" style="max-width: 120px; max-height: 120px;" />
                <?php else: ?>
                    <span>No image uploaded.</span>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label">Change Cover Image (JPG/PNG, optional)</label>
                <input type="file" name="cover" class="form-control" accept="image/*" />
            </div>

            <div class="mb-3">
                <label class="form-label">Current Book PDF</label><br />
                <?php if (!empty($book['pdf'])): ?>
                    <a href="<?= $path . htmlspecialchars($book['pdf']); ?>" target="_blank">View PDF</a>
                <?php else: ?>
                    <span>No PDF uploaded.</span>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label">Change Book PDF (PDF, optional)</label>
                <input type="file" name="pdf" class="form-control" accept="application/pdf" />
            </div>

            <div class="d-flex justify-content-between flex-column flex-md-row gap-3 mt-4">
                <button class="btn btn-dark">Update Book</button>
                <a class="float-end icon-link icon-link-hover text-decoration-none" href="<?= APP_URL ?>/src/views/author/dashboard">
                    <svg xmlns="http://www.w3.org/2000/svg" class="bi" viewBox="0 0 16 16" width="16" height="16" aria-hidden="true">
                        <path d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z" />
                    </svg>
                    Back to dashboard
                </a>
            </div>
        </form>

    </div>
</div>

<?php require __DIR__ . "/../Components/footer.php"; ?>