<?php

require_once __DIR__ . "/../views/Components/layout.php";

if (isset($_SESSION['author_id'])) {
    header("Location: " . APP_URL . "/views/Author/dashboard");
    exit();
}
if (isset($_SESSION['user_id'])) {
    header("Location: " . APP_URL . "/views/User/dashboard");
    exit();
}

$limit = 6; // books per page
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



<main class="container my-5">
    <div class="row">
        <?php
        $sql = "SELECT books.id, books.name AS book_name, books.vol, books.img, authors.name AS author_name, books.pdf
                FROM books
                JOIN authors ON books.author_id = authors.id
                ORDER BY books.created_at DESC
                LIMIT $limit OFFSET $offset";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $book_id = $row["id"];
                $path = APP_URL . "/public/assets/uploads/";
                $coverImage = !empty($row["img"]) ?  $path . htmlspecialchars($row["img"]) : 'https://via.placeholder.com/300x180?text=No+Image';

                echo '
                    <div class="col-md-4 mb-4">
                        <div class="card shadow h-100 d-flex flex-column">
                            <img src="' . $coverImage . '" class="card-img-top" style="height: 250px; object-fit: cover;" alt="Book Cover">
                            <div class="card-body">
                                <h5 class="card-title">' . htmlspecialchars($row["book_name"]) . '</h5>
                                <h6 class="card-subtitle mb-2 text-muted">by ' . htmlspecialchars($row["author_name"]) . '</h6>
                                <p class="card-text">Volume: ' . htmlspecialchars($row["vol"]) . '</p>

                                <div class="d-grid gap-2">
                                    <button class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#readModal' . $book_id . '">Read Online</button>
                                    <button class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#readModal' . $book_id . '">Download PDF</button>
                                </div>

                                <form action="" method="post" class="mt-3">
                                    <input type="hidden" name="book_id" value="' . $book_id . '">
                                    <div class="mb-2">
                                        <input type="text" name="user_name" class="form-control" placeholder="Your Name" required>
                                    </div>
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
                                    <button type="submit" class="btn btn-sm btn-secondary">Submit</button>
                                </form>

                                <button class="btn btn-outline-secondary btn-sm mt-3 w-100" type="button" data-bs-toggle="collapse" data-bs-target="#reviews' . $book_id . '">
                                    Show Reviews
                                </button>
                                <div class="collapse mt-3" id="reviews' . $book_id . '">
                                    <h6>Recent Reviews:</h6>';

                $review_sql = "SELECT * FROM reviews WHERE book_id = $book_id ORDER BY created_at DESC LIMIT 3";
                $reviews = $conn->query($review_sql);

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
                    </div>

                    <div class="modal fade" id="readModal' . $book_id . '" tabindex="-1" aria-labelledby="readModalLabel' . $book_id . '" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered ">
                            <div class="modal-content">
                                <div class="modal-header bg-dark text-white">
                                    <h5 class="modal-title" id="readModalLabel' . $book_id . '">Login Required</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Please login to access reading or downloading this book.
                                </div>
                                <div class="modal-footer">
                                    <a href="' . AUTH_URL . '/User/Login?message=Please+login+to+read&book_id=' . $book_id . '" class="btn btn-outline-dark">Login</a>
                                </div>
                            </div>
                        </div>
                    </div>';
            }
        } else {
            echo '<div class="col-12 text-center"><p>No books found.</p></div>';
        }
        ?>
    </div>

    <?php
    // PAGINATION
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

<?php require __DIR__ . "/../views/Components/footer.php"; ?>

<div class="modal fade" id="reviewSuccessModal" tabindex="-1" aria-labelledby="reviewSuccessModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="reviewSuccessModalLabel">Review Submitted</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Thank you! Your review has been successfully submitted.
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
</body>

</html>