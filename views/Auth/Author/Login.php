<?php
require __DIR__ . "/../../../App/app.php";
require __DIR__ . "/../../../config/url.php";

$app = new App();
$conn = $app->conn;
session_start();

if (isset($_SESSION['author_id']) || isset($_SESSION['user_id'])) {
    header("Location: " . APP_URL . "/views/Author/dashboard");
    exit();
}
?>

<?php require __DIR__ . "/../../Components/layout.php"; ?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-10 col-md-8 col-lg-6">
            <div class="card shadow-sm border p-4">
                <h2 class="mb-4 text-center">Log In as Author</h2>

                <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $email = trim($_POST['email']);
                    $password = trim($_POST['password']);

                    // Use app.php to get author by email
                    $author = $app->getAuthorByEmail($email);

                    // $author = $app->getAuthorByEmail($email); already called above
                    // But $author is actually the result set, so let's rename for clarity
                    $result = $author; // $author is actually the result set from getAuthorByEmail

                    if ($result && $result->num_rows == 1) {
                        $authorData = $result->fetch_assoc();

                        if (password_verify($password, $authorData['password'])) {
                            session_regenerate_id(true);
                            $_SESSION['author'] = $authorData['email'];
                            $_SESSION['author_id'] = $authorData['id'];
                            $_SESSION['author_name'] = $authorData['name'];


                            header("Location: " . APP_URL . "/views/Author/dashboard");
                            exit();
                        } else {
                            $error = "<div class='alert alert-danger'>Invalid password.</div>";
                        }
                    } else {
                        $error = "<div class='alert alert-danger'>No user found with this email.</div>";
                    }

                    if (isset($result) && $result instanceof mysqli_result) {
                        $result->close();
                    }
                }


                ?>

                <form method="post" novalidate>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" required />
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required />
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-dark">Login</button>
                    </div>

                    <div class="mt-3 text-center small">
                        <a href="<?php echo AUTH_URL ?>/Author/Register" class="d-block mb-1 icon-link icon-link-hover link-body-emphasis link-offset-2 link-underline-opacity-25 link-underline-opacity-75-hover">Don't have an account?</a>
                        <a href="<?php echo AUTH_URL; ?>/User/Login" class="d-block icon-link icon-link-hover link-body-emphasis link-offset-2 link-underline-opacity-25 link-underline-opacity-75-hover">Log in as User</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . "/../../Components/footer.php"; ?>
</body>

</html>