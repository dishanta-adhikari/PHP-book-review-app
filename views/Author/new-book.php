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

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $vol = intval($_POST['vol']);
    $author_id = $_SESSION['author_id'];

    // Handle file uploads
    $img_new = null;
    $pdf_new = null;

    // Cover image upload
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
    } else {
        $message = "<div class='alert alert-danger'>Cover image is required.</div>";
    }

    // PDF upload
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
    } else {
        $message = "<div class='alert alert-danger'>Book PDF is required.</div>";
    }

    // Only proceed if both files are uploaded and no error message
    if ($img_new && $pdf_new && empty($message)) {
        $result = $app->createBook($name, $vol, $author_id, $img_new, $pdf_new);
        if ($result) {
            $message = "<div class='alert alert-success'>Book added successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Failed to add book. Please try again.</div>";
        }
    }
}
?>

<?php require __DIR__ . "/../Components/layout.php"; ?>

<div class="container-sm my-5">
    <div class="card shadow-sm p-4">
        <h2 class="mb-4 text-center text-md-start">Add New Book</h2>

        <?php echo $message; ?>

        <form method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
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
                <button class="btn btn-dark">Add Book</button>
                <a class="float-end icon-link icon-link-hover text-decoration-none" href="<?php echo APP_URL; ?>/views/Author/dashboard">
                    <svg xmlns="http://www.w3.org/2000/svg" class="bi" viewBox="0 0 16 16" width="16" height="16" aria-hidden="true">
                        <path d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z" />
                    </svg>
                    Click here access dashboard
                </a>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . "/../Components/footer.php"; ?>


