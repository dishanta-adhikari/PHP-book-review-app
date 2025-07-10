<?php
require __DIR__ . "/../../App/app.php";
require __DIR__ . "/../../config/url.php";

$app = new App();
$conn = $app->conn;
session_start();

if (!isset($_SESSION['user_name'])) {
    header("Location: " . AUTH_URL . "/User/Login");
    exit();
}

if (isset($_SESSION['login_success'])) {
    echo "<div class='alert alert-success text-center'>" . $_SESSION['login_success'] . "</div>";
    unset($_SESSION['login_success']); // show once
}

// Pagination setup
$limit = 6;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;


$showSuccessModal = false;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["book_id"])) {
    $book_id = $_POST["book_id"];
    $user_name = $_POST["user_name"];
    $rating = $_POST["rating"];
    $comment = $_POST["comment"];

    $stmt = $conn->prepare("INSERT INTO reviews (book_id, user_name, rating, comment) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isis", $book_id, $user_name, $rating, $comment);

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: " . $_SERVER['PHP_SELF'] . "?review_submitted=1");
        exit();
    }
    $stmt->close();
}

if (isset($_GET['review_submitted'])) {
    $showSuccessModal = true;
}

?>

<?php require __DIR__ . "/../Components/layout.php"; ?>

<!-- Welcome message below nav header -->
<?php if (isset($_SESSION['user_name'])): ?>
    <div class="container mt-3">
        <div class="alert alert-info text-center mb-4">
            Welcome, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>
        </div>
    </div>
<?php endif; ?>

<main class="container my-4">
    <div class="row">
        <?php
        
        $books = $app->getBooksWithAuthors($limit, $offset);

        
        if ($books && $books->num_rows > 0) {
            while ($row = $books->fetch_assoc()) {
                $book_id = $row["id"];
                $book_name = htmlspecialchars($row["name"]);
                $author_name = htmlspecialchars($row["author_name"]);
                $volume = htmlspecialchars($row["vol"]);
                $coverImage = !empty($row["img"]) ? '../../uploads/' . htmlspecialchars($row["img"]) : 'https://via.placeholder.com/300x180?text=No+Image';
                $pdfLink = !empty($row["pdf"]) ? '../../uploads/' . htmlspecialchars($row["pdf"]) : '';

                echo '
                    <div class="col-md-4 mb-4">
                        <div class="card shadow h-100 d-flex flex-column">
                            <img src="' . $coverImage . '" class="card-img-top" style="height: 250px; object-fit: cover;" alt="Book Cover">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">' . $book_name . '</h5>
                                <h6 class="card-subtitle mb-2 text-muted">by ' . $author_name . '</h6>
                                <p class="card-text">Volume: ' . $volume . '</p>';

                if ($pdfLink) {
                    echo '
                                <div class="d-grid gap-2 mb-3">
                                    <a href="' . $pdfLink . '" class="btn btn-outline-primary btn-sm" target="_blank">Read Online</a>
                                    <a href="' . $pdfLink . '" class="btn btn-outline-success btn-sm" download>Download PDF</a>
                                </div>';
                }

                echo '
                                <button class="btn btn-outline-secondary btn-sm mt-auto" type="button" data-bs-toggle="collapse" data-bs-target="#expand' . $book_id . '">
                                    Show More
                                </button>

                                <div class="collapse mt-3" id="expand' . $book_id . '">
                                    <form action="" method="post">
                                        <input type="hidden" name="book_id" value="' . $book_id . '">
                                        <input type="hidden" name="user_name" value="' . htmlspecialchars($_SESSION['user_name']) . '">
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

                                    <h6 class="mt-3">Recent Reviews:</h6>';

                // Use App class method to get recent reviews for this book
                $reviews = $app->getRecentReviewsByBookId($book_id, 3);

                if ($reviews && $reviews->num_rows > 0) {
                    while ($review = $reviews->fetch_assoc()) {
                        echo '
                                    <div class="border rounded p-2 mb-2">
                                        <strong>' . htmlspecialchars($review["user_name"]) . '</strong>
                                        <span class="text-warning">' . str_repeat("★", $review["rating"]) . str_repeat("☆", 5 - $review["rating"]) . '</span><br>
                                        <small>' . htmlspecialchars($review["comment"]) . '</small>
                                    </div>';
                    }
                } else {
                    echo '<p class="text-muted">No reviews yet.</p>';
                }

                echo '
                                </div>
                            </div>
                        </div>
                    </div>';
            }
        } else {
            echo '<div class="col-12"><p class="text-center">No books available.</p></div>';
        }
        ?>
    </div>

    <?php
    $total_books_result = $conn->query("SELECT COUNT(*) AS total FROM books");
    $total_books_row = $total_books_result->fetch_assoc();
    $total_books = $total_books_row['total'];
    $total_pages = ceil($total_books / $limit);
    ?>

    <?php if ($total_pages > 1): ?>
        <nav aria-label="Page navigation" class="my-4">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
                    <a class="page-link" href="?page=<?php echo max(1, $page - 1); ?>">Previous</a>
                </li>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>

                <li class="page-item <?php if ($page >= $total_pages) echo 'disabled'; ?>">
                    <a class="page-link" href="?page=<?php echo min($total_pages, $page + 1); ?>">Next</a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</main>

<?php require __DIR__ . "/../Components/footer.php"; ?>

<div class="modal fade" id="reviewSuccessModal" tabindex="-1" aria-labelledby="reviewSuccessModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="reviewSuccessModalLabel">Review Submitted</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                ✅ Thank you! Your review has been successfully submitted.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<?php if ($showSuccessModal): ?>
    <script>
        var successModal = new bootstrap.Modal(document.getElementById('reviewSuccessModal'));
        window.addEventListener('load', function() {
            successModal.show();
        });
    </script>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>

</html>