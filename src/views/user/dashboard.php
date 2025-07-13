<?php
require_once __DIR__ . "/../Components/layout.php";

use App\Helpers\Flash;
use App\Middleware\SessionMiddleware;
use App\Controllers\BookController;
use App\Controllers\ReviewController;

SessionMiddleware::validateLoggedInSession('user'); //ensure user is logged in

$bookController = new BookController($conn);
$reviewController = new ReviewController($conn);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $reviewController->create($_POST);
}

// Pagination setup
$limit = 6;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$books = $bookController->getBooksWithAuthors($offset);

$total_pages = $bookController->totalPages($limit);

Flash::render();  // Welcome message below nav header 

?>

<main class="container my-4">
    <div class="row">
        <?php foreach ($books as $row): ?>
            <?php
            $book_id = $row["id"];
            $book_name = htmlspecialchars($row["name"]);
            $author_name = htmlspecialchars($row["author_name"]);
            $volume = htmlspecialchars($row["vol"]);
            $path = APP_URL . "/public/assets/uploads/";
            $coverImage = !empty($row["img"]) ? $path . htmlspecialchars($row["img"]) : 'https://via.placeholder.com/300x180?text=No+Image';
            $pdfLink = !empty($row["pdf"]) ? $path . htmlspecialchars($row["pdf"]) : '';
            $reviews = $reviewController->getRecentReviewsByBookId($book_id);
            ?>
            <div class="col-md-4 mb-4">
                <div class="card shadow h-100 d-flex flex-column">
                    <img src="<?= $coverImage ?>" class="card-img-top" style="height: 250px; object-fit: cover;" alt="Book Cover">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= $book_name ?></h5>
                        <h6 class="card-subtitle mb-2 text-muted">by <?= $author_name ?></h6>
                        <p class="card-text">Volume: <?= $volume ?></p>

                        <?php if ($pdfLink): ?>
                            <div class="d-grid gap-2 mb-3">
                                <a href="<?= $pdfLink ?>" class="btn btn-outline-primary btn-sm" target="_blank">Read Online</a>
                                <a href="<?= $pdfLink ?>" class="btn btn-outline-success btn-sm" download>Download PDF</a>
                            </div>
                        <?php endif; ?>

                        <button class="btn btn-outline-secondary btn-sm mt-auto" type="button" data-bs-toggle="collapse" data-bs-target="#expand<?= $book_id ?>">
                            Show More
                        </button>

                        <div class="collapse mt-3" id="expand<?= $book_id ?>">
                            <form action="" method="post">
                                <input type="hidden" name="book_id" value="<?= $book_id ?>">
                                <input type="hidden" name="user_name" value="<?= htmlspecialchars($_SESSION['user_name']) ?>">
                                <div class="mb-2">
                                    <select name="rating" class="form-select" required>
                                        <option value="">Rating</option>
                                        <option value="5">★★★★★</option>
                                        <option value="4">★★★★☆</option>
                                        <option value="3">★★★☆☆</option>
                                        <option value="2">★★☆☆☆</option>
                                        <option value="1">★☆☆☆☆</option>
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <textarea name="comment" class="form-control" placeholder="Write a review..." rows="2" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-sm btn-primary w-100">Submit Review</button>
                            </form>

                            <h6 class="mt-3">Recent Reviews:</h6>
                            <?php foreach ($reviews as $rev): ?>
                                <div class="border rounded p-2 mb-2">
                                    <strong><?= htmlspecialchars($rev["user_name"]) ?></strong>
                                    <span class="text-warning"><?= str_repeat("★", $rev["rating"]) . str_repeat("☆", 5 - $rev["rating"]) ?></span><br>
                                    <small><?= htmlspecialchars($rev["comment"]) ?></small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($total_pages > 1): ?>
        <nav aria-label="Page navigation" class="my-4">
            <ul class="pagination justify-content-center">
                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= max(1, $page - 1) ?>">Previous</a>
                </li>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= min($total_pages, $page + 1) ?>">Next</a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . "/../Components/footer.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>

</html>