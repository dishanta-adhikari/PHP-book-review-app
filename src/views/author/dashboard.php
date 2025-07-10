<?php
require __DIR__ . "/../../App/app.php";
require __DIR__ . "/../../config/url.php";

$app = new App();
$conn = $app->conn;
session_start();

if (!isset($_SESSION['author_id'])) {
    header("Location: " . AUTH_URL . "/Author/Login");
    exit();
}

if (isset($_SESSION['login_success'])) {
    echo "<div class='alert alert-success text-center'>" . $_SESSION['login_success'] . "</div>";
    unset($_SESSION['login_success']); // show once
}

$author_id = (int)$_SESSION['author_id'];
$alert = "";

// Handle Delete Request using App class
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_book_id'])) {
    $book_id = (int)$_POST['delete_book_id'];
    $input_password = $_POST['confirm_password'];

    // Get author password securely using App class
    $author = $app->getAuthorById($author_id);

    if ($author && password_verify($input_password, $author['password'])) {
        // Use App class to delete book
        $book = $app->getBookById($book_id);
        if ($book && $book['author_id'] == $author_id) {
            $app->deleteBook($book_id);
            $alert = "<div class='alert alert-success text-center'>Book deleted successfully.</div>";
        } else {
            $alert = "<div class='alert alert-danger text-center'>Book not found or unauthorized.</div>";
        }
    } else {
        $alert = "<div class='alert alert-danger text-center'>Invalid password. Book not deleted.</div>";
    }
}

// Pagination
$limit = 6;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch books using App class with pagination
$books = $app->getBooksByAuthor($author_id, $limit, $offset);

// For total books count (for pagination)
$total_books = $app->getBooksCountByAuthor($author_id);
$total_pages = ceil($total_books / $limit);

// Get author name for welcome message
$author_name = isset($_SESSION['author_name']) ? $_SESSION['author_name'] : '';
?>

<?php require __DIR__ . "/../Components/layout.php"; ?>

<!-- Welcome message below nav header -->
<?php if ($author_name): ?>
    <div class="container mt-3">
        <div class="alert alert-info text-center mb-4">
            Welcome, <strong><?php echo htmlspecialchars($author_name); ?></strong>!
        </div>
    </div>
<?php endif; ?>

<main class="container my-4">
    <?php echo $alert; ?>

    <h4 class="mb-4 text-center text-md-start">Books Added by You</h4>
    <div class="row g-3">
        <?php
        if ($books && count($books) > 0) {
            foreach ($books as $book) {
                $book_id = $book["id"];
                $coverImage = !empty($book["img"]) ? '../../uploads/' . htmlspecialchars($book["img"]) : 'https://via.placeholder.com/300x180?text=No+Image';
                $pdfLink = !empty($book["pdf"]) ? '../../uploads/' . htmlspecialchars($book["pdf"]) : '';

                echo '
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="card h-100 shadow-sm d-flex flex-column">
                            <div class="ratio ratio-16x9">
                                <img src="' . $coverImage . '" class="card-img-top img-fluid" style="object-fit: cover;" alt="Book Cover">
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">' . htmlspecialchars($book["name"]) . '</h5>
                                <p class="card-text">Volume: ' . htmlspecialchars($book["vol"]) . '</p>';

                if ($pdfLink) {
                    echo '
                                <div class="mb-2 d-flex flex-wrap gap-2">
                                    <a href="' . $pdfLink . '" target="_blank" class="btn btn-sm btn-primary flex-fill">Read Online</a>
                                    <a href="' . $pdfLink . '" download class="btn btn-sm btn-outline-success flex-fill">Download PDF</a>
                                </div>';
                }

                // Add Edit button here
                echo '
                                <a href="' . APP_URL . '/views/Author/edit-book?id=' . $book_id . '" class="btn btn-warning btn-sm w-100 mb-2">Edit</a>';

                echo '
                                <button 
                                    class="btn btn-outline-secondary btn-sm mt-auto w-100 d-flex justify-content-between align-items-center collapsed"
                                    type="button" 
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#reviews' . $book_id . '"
                                    aria-expanded="false"
                                    aria-controls="reviews' . $book_id . '"
                                >
                                    <span>Show Reviews</span>
                                    <i class="bi bi-chevron-down"></i>
                                </button>
                                <div class="collapse mt-2" id="reviews' . $book_id . '">
                                    <div class="card card-body p-2">
                                        <em>No reviews loaded (demo placeholder).</em>
                                    </div>
                                </div>
                                <form method="post" class="mt-3">
                                    <input type="hidden" name="delete_book_id" value="' . $book_id . '">
                                    <div class="mb-2">
                                        <input type="password" name="confirm_password" class="form-control form-control-sm" placeholder="Confirm password to delete" required>
                                    </div>
                                    <button type="submit" class="btn btn-danger btn-sm w-100" onclick="return confirm(\'Are you sure you want to delete this book?\')">Delete Book</button>
                                </form>
                            </div>
                        </div>
                    </div>
                ';
            }
        } else {
            echo '<div class="col-12"><p class="text-center">You haven\'t added any books yet.</p></div>';
        }
        ?>
    </div>

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

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

<style>
    button[aria-expanded="true"] i {
        transform: rotate(180deg);
        transition: transform 0.3s;
    }
</style>

<?php require __DIR__ . "/../Components/footer.php"; ?>
</body>
</html>
