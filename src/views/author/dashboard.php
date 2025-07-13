<?php
require_once __DIR__ . "/../Components/layout.php";

use App\Middleware\SessionMiddleware;
use App\Controllers\BookController;
use App\Controllers\ReviewController;
use App\Helpers\Flash;

SessionMiddleware::validateLoggedInSession('author');

$book = new BookController($conn);
$review = new ReviewController($conn);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $book->delete($_POST);
}

// Pagination Setup
$limit = 6;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$total_pages = $book->totalPages($limit);

$author_id = $_SESSION['author_id'];

$books = $book->getBooksByAuthorId($author_id);

?>

<?php Flash::render(); ?>


<main class="container my-4">
    <h4 class="mb-4 text-center text-md-start">Books Added by You</h4>
    <div class="row g-3">
        <?php if ($books) : ?>
            <?php foreach ($books as $book) : ?>
                <?php $book_id = $book["id"]; ?>
                <?php $_SESSION['db_author_id'] = $book["author_id"]; ?>
                <?php $path = APP_URL . "/public/assets/uploads/"; ?>
                <?php $coverImage = !empty($book["img"]) ?  $path . htmlspecialchars($book["img"]) : 'https://via.placeholder.com/300x180?text=No+Image'; ?>
                <?php $pdfLink = !empty($book["pdf"]) ?  $path . htmlspecialchars($book["pdf"]) : ''; ?>

                <div class="col-12 col-sm-6 col-md-4">
                    <div class="card h-100 shadow-sm d-flex flex-column">
                        <div class="ratio ratio-16x9">
                            <img src="<?= $coverImage ?>" class="card-img-top img-fluid" style="object-fit: cover;" alt="Book Cover">
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($book["name"]) ?></h5>
                            <p class="card-text">Volume: <?= htmlspecialchars($book["vol"]) ?></p>

                            <?php if ($pdfLink) : ?>
                                <div class="mb-2 d-flex flex-wrap gap-2">
                                    <a href="<?= $pdfLink ?>" target="_blank" class="btn btn-sm btn-primary flex-fill">Read Online</a>
                                    <a href="<?= $pdfLink ?>" download class="btn btn-sm btn-outline-success flex-fill">Download PDF</a>
                                </div>
                            <?php endif; ?>

                            <a href="<?= APP_URL ?>/src/views/book/edit?id=<?= $book_id ?>" class="btn btn-warning btn-sm w-100 mb-2">Edit</a>

                            <button
                                class="btn btn-outline-secondary btn-sm mt-auto w-100 d-flex justify-content-between align-items-center collapsed"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#reviews<?= $book_id ?>"
                                aria-expanded="false"
                                aria-controls="reviews<?= $book_id ?>">
                                <span>Show Reviews</span>
                                <i class="bi bi-chevron-down"></i>
                            </button>

                            <div class="collapse mt-2" id="reviews<?= $book_id ?>">
                                <div class="card card-body p-2">

                                    <?php $reviews = $review->getRecentReviewsByBookId($book_id); ?>
                                    <?php if ($reviews && count($reviews) > 0): ?>
                                        <?php foreach ($reviews as $r): ?>
                                            <div class="border rounded p-2 mb-2">
                                                <strong><?= htmlspecialchars($r["user_name"]) ?></strong>
                                                <span class="text-warning"><?= str_repeat("★", $r["rating"]) . str_repeat("☆", 5 - $r["rating"]) ?></span><br>
                                                <small><?= htmlspecialchars($r["comment"]) ?></small>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="text-muted">No reviews yet.</p>
                                    <?php endif; ?>

                                </div>
                            </div>


                            <form method="POST" class="mt-3">
                                <input type="hidden" name="book_id" value="<?= $book_id ?>">
                                <input type="hidden" name="author_id" value="<?= $_SESSION['author_id'] ?>">
                                <div class="mb-2">
                                    <input type="password" name="password" class="form-control form-control-sm" placeholder="Confirm password to delete" required>
                                </div>
                                <button type="submit" class="btn btn-danger btn-sm w-100" onclick="return confirm('Are you sure you want to delete this book?')">Delete Book</button>
                            </form>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="col-12">
                <p class="text-center">You haven’t added any books yet.</p>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($total_pages > 1): ?>
        <nav aria-label="Page navigation" class="my-4">
            <ul class="pagination justify-content-center">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= max(1, $page - 1) ?>">Previous</a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= min($total_pages, $page + 1) ?>">Next</a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</main>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

<style>
    button[aria-expanded="true"] i {
        transform: rotate(180deg);
        transition: transform 0.3s;
    }
</style>

<?php require_once __DIR__ . "/../Components/footer.php"; ?>
</body>

</html>