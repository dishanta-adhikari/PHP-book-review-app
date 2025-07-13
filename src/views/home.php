<?php
require_once __DIR__ . "/../views/Components/layout.php";

use App\Middleware\SessionMiddleware;
use App\Controllers\ReviewController;
use App\Controllers\BookController;
use App\Helpers\Flash;

SessionMiddleware::verifySession('user');       //verify the user , it is a static(state-less) method call
SessionMiddleware::verifySession('author');     //verify the author , it is a static(state-less) method call

$review = new ReviewController($conn);
$book = new BookController($conn);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $review->create($_POST);    //submit a review
}

$limit = 6; // books per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$total_pages = $book->totalPages($limit);

$books = $book->getAllBooks($offset);

?>


<main class="container my-5">
    <?php Flash::render(); ?> <!-- display the session messages  -->
    <div class="row">

        <?php if ($books): ?>
            <?php foreach ($books as $row): ?>
                <?php $book_id = $row['id']; ?>
                <?php $cover = !empty($row['img']) ? APP_URL . "/public/assets/uploads/" . htmlspecialchars($row['img']) : 'https://via.placeholder.com/300x180?text=No+Image'; ?>
                <?php $pdf = !empty($row['pdf']) ? APP_URL . "/public/assets/uploads/" . htmlspecialchars($row['pdf']) : ''; ?>

                <div class="col-md-4 mb-4">
                    <div class="card shadow h-100 d-flex flex-column">
                        <img src="<?= $cover ?>" class="card-img-top" style="height: 250px; object-fit: cover;" alt="Book Cover">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($row['book_name']) ?></h5>
                            <h6 class="card-subtitle mb-2 text-muted">by <?= htmlspecialchars($row['author_name']) ?></h6>
                            <p class="card-text">Volume: <?= htmlspecialchars($row['vol']) ?></p>

                            <div class="d-grid gap-2">
                                <button class="btn btn-dark btn-sm" onclick="window.location.href='<?= AUTH_URL . "/user/login";
                                                                                                    $_SESSION['info'] = "Please log in/register to read or download books."; ?>';">Read Online</button>
                                <button class="btn btn-outline-dark btn-sm" onclick="window.location.href='<?= AUTH_URL . "/user/login";
                                                                                                            $_SESSION['info'] = "Please log in/register to read or download books."; ?>';">Download PDF</button>
                            </div>

                            <form action="" method="post" class="mt-3"> <!-- Review Form -->
                                <input type="hidden" name="book_id" value="<?= $book_id ?>">
                                <?php if (!isset($_SESSION['user_name'])): ?>
                                    <input type="text" name="user_name" class="form-control mb-2" placeholder="Your Name" required>
                                <?php else: ?>
                                    <input type="hidden" name="user_name" value="<?= htmlspecialchars($_SESSION['user_name']) ?>">
                                <?php endif; ?>
                                <select name="rating" class="form-select mb-2" required>
                                    <option value="">Rating</option>
                                    <option value="5">★★★★★</option>
                                    <option value="4">★★★★☆</option>
                                    <option value="3">★★★☆☆</option>
                                    <option value="2">★★☆☆☆</option>
                                    <option value="1">★☆☆☆☆</option>
                                </select>
                                <textarea name="comment" class="form-control mb-2" placeholder="Write a review..." rows="2" required></textarea>
                                <button type="submit" class="btn btn-secondary btn-sm w-100">Submit</button>
                            </form>

                            <!-- Reviews Section -->
                            <button class="btn btn-outline-secondary btn-sm mt-3 w-100" type="button" data-bs-toggle="collapse" data-bs-target="#reviews<?= $book_id ?>">
                                Show Reviews
                            </button>
                            <div class="collapse mt-3" id="reviews<?= $book_id ?>">
                                <h6>Recent Reviews:</h6>

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
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center">
                <p>No books found.</p>
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

<?php require __DIR__ . "/Components/footer.php"; ?>

</body>

</html>