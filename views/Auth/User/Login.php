<?php
require __DIR__ . "/../../../App/app.php";
require __DIR__ . "/../../../config/url.php";

$app = new App();
$conn = $app->conn;
session_start();

// Redirect users and authors to their respective dashboards
if (isset($_SESSION['user_id'])) {
    header("Location: " . APP_URL . "/views/User/dashboard.php");
    exit();
}
if (isset($_SESSION['author_id'])) {
    header("Location: " . APP_URL . "/views/Author/dashboard.php");
    exit();
}
?>

<?php require __DIR__ . "/../../Components/layout.php"; ?>

<body class="bg-light">
    <main class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 col-sm-10">
                <div class="card shadow-sm border rounded p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="mb-0">Log In</h2>
                        <a href="<?php echo AUTH_URL; ?>/Author/Login"
                            class="icon-link icon-link-hover link-body-emphasis link-offset-2 link-underline-opacity-25 link-underline-opacity-75-hover small">
                            Login as Author
                        </a>
                    </div>

                    <?php
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        $email = trim($_POST['email']);
                        $password = trim($_POST['password']);

                        // Try user login first
                        $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
                        $stmt->bind_param("s", $email);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result && $result->num_rows === 1) {
                            $user = $result->fetch_assoc();

                            if (password_verify($password, $user['password'])) {
                                session_regenerate_id(true);
                                $_SESSION['user_id'] = $user['id'];
                                $_SESSION['user_name'] = $user['name'];

                            

                                // Redirect immediately
                                header("Location: " . APP_URL . "/views/User/dashboard");
                                exit();
                            } else {
                                $error = "<div class='alert alert-danger'>Invalid password.</div>";
                            }
                        } else {
                            // Try author login if not found as user
                            $stmt2 = $conn->prepare("SELECT id, name, password FROM authors WHERE email = ?");
                            $stmt2->bind_param("s", $email);
                            $stmt2->execute();
                            $result2 = $stmt2->get_result();

                            if ($result2 && $result2->num_rows === 1) {
                                $author = $result2->fetch_assoc();

                                if (password_verify($password, $author['password'])) {
                                    session_regenerate_id(true);
                                    $_SESSION['author_id'] = $author['id'];
                                    $_SESSION['author_name'] = $author['name'];

                                    // Set a flash message to show on author dashboard
                                    $_SESSION['login_success'] = "Welcome back, Author " . htmlspecialchars($author['name']) . "!";

                                    // Redirect to author dashboard
                                    header("Location: " . APP_URL . "/views/Author/dashboard");
                                    exit();
                                } else {
                                    $error = "<div class='alert alert-danger'>Invalid password.</div>";
                                }
                            } else {
                                $error = "<div class='alert alert-danger'>No user or author found with this email.</div>";
                            }
                            $stmt2->close();
                        }

                        $stmt->close();
                    }

                    if (isset($error)) {
                        echo $error;
                    }
                    ?>

                    <form method="post" class="mt-3">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" required />
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" id="password" class="form-control" required />
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="submit" class="btn btn-dark">Login</button>
                            <a href="<?php echo AUTH_URL ?>/User/Register"
                                class="small icon-link icon-link-hover link-body-emphasis link-offset-2 link-underline-opacity-25 link-underline-opacity-75-hover">
                                Don't have an account?
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php require __DIR__ . "/../../Components/footer.php"; ?>
</body>

</html>