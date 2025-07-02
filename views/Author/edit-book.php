<?php
require_once __DIR__ . "/../../App/app.php";
require_once __DIR__ . "/../../config/url.php";
session_start();

$app = new App();
$message = "";

// Check if author is logged in
if (!isset($_SESSION['author_id'])) {
    header("Location: " . APP_URL . "/views/Auth/Author/Login");
    exit();
}

$author_id = $_SESSION['author_id'];

// Get book ID from query string
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: " . APP_URL . "/views/Author/dashboard");
    exit();
}
$book_id = (int)$_GET['id'];
$book = $app->getBookById($book_id);

if (!$book || $book['author_id'] != $author_id) {
    $message = "<div class='alert alert-danger'>Book not found or unauthorized.</div>";
} else {
    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $name = trim($_POST['name']);
        $vol = intval($_POST['vol']);
        $img_new = null;
        $pdf_new = null;

        // Cover image upload (optional)
        if (isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
            $img_tmp = $_FILES['cover']['tmp_name'];
            $img_ext = strtolower(pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION));
            $allowed_img = ['jpg', 'jpeg', 'png'];
            if (in_array($img_ext, $allowed_img)) {
                $img_new = uniqid("cover_", true) . "." . $img_ext;
                $img_dest = __DIR__ . "/../../uploads/covers/" . $img_new;
                if (!is_dir(dirname($img_dest))) {
                    mkdir(dirname($img_dest), 0777, true);
                }
                move_uploaded_file($img_tmp, $img_dest);
            } else {
                $message = "<div class='alert alert-danger'>Invalid image file type.</div>";
            }
        }

        // PDF upload (optional)
        if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] === UPLOAD_ERR_OK) {
            $pdf_tmp = $_FILES['pdf']['tmp_name'];
            $pdf_ext = strtolower(pathinfo($_FILES['pdf']['name'], PATHINFO_EXTENSION));
            if ($pdf_ext === "pdf") {
                $pdf_new = uniqid("book_", true) . ".pdf";
                $pdf_dest = __DIR__ . "/../../uploads/pdfs/" . $pdf_new;
                if (!is_dir(dirname($pdf_dest))) {
                    mkdir(dirname($pdf_dest), 0777, true);
                }
                move_uploaded_file($pdf_tmp, $pdf_dest);
            } else {
                $message = "<div class='alert alert-danger'>Invalid PDF file type.</div>";
            }
        }

        // Only update if no error message
        if (empty($message)) {
            $result = $app->updateBook($book_id, $name, $vol, $img_new, $pdf_new);
            if ($result) {
                $message = "<div class='alert alert-success'>Book updated successfully!</div>";
                // Refresh book data
                $book = $app->getBookById($book_id);
            } else {
                $message = "<div class='alert alert-danger'>Failed to update book. Please try again.</div>";
            }
        }
    }
}
?>

<?php require __DIR__ . "/../Components/layout.php"; ?>

<div class="container-sm my-5">
    <div class="card shadow-sm p-4">
        <h2 class="mb-4 text-center text-md-start">Edit Book</h2>

        <?php echo $message; ?>

        <?php if ($book && $book['author_id'] == $author_id): ?>
        <form method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
            <div class="mb-3">
                <label class="form-label">Book Name</label>
                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($book['name']); ?>" required />
            </div>

            <div class="mb-3">
                <label class="form-label">Volume</label>
                <input type="number" name="vol" class="form-control" value="<?php echo htmlspecialchars($book['vol']); ?>" required />
            </div>

            <div class="mb-3">
                <label class="form-label">Current Cover Image</label><br />
                <?php if (!empty($book['img'])): ?>
                    <img src="../../uploads/<?php echo htmlspecialchars($book['img']); ?>" alt="Cover" style="max-width: 120px; max-height: 120px;" />
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
                    <a href="../../uploads/<?php echo htmlspecialchars($book['pdf']); ?>" target="_blank">View PDF</a>
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
                <a class="float-end icon-link icon-link-hover text-decoration-none" href="<?php echo APP_URL; ?>/views/Author/dashboard">
                    <svg xmlns="http://www.w3.org/2000/svg" class="bi" viewBox="0 0 16 16" width="16" height="16" aria-hidden="true">
                        <path d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z" />
                    </svg>
                    Back to dashboard
                </a>
            </div>
        </form>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . "/../Components/footer.php"; ?> 