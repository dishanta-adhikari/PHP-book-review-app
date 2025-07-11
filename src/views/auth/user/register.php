<?php
require_once __DIR__ . "/../../../_init_.php";

if (isset($_SESSION['user_id']) || isset($_SESSION['author_id'])) {
    header("Location:" . APP_URL . " /views/User/dashboard"); // Change this to your dashboard path
    exit();
}


$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = $conn->real_escape_string($_POST['name']);
    $email    = $conn->real_escape_string($_POST['email']);
    $phone    = (int)$_POST['phone'];
    $address  = $conn->real_escape_string($_POST['address']);
    $password = trim($_POST['password']); // raw password, only trimming

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $message = "<div class='alert alert-danger'>Email already registered.</div>";
    } else {
        $stmt->close();

        // NOW hash once
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (name, email, phone, address, password, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssiss", $name, $email, $phone, $address, $hashedPassword);

        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>Registration successful. You can login now.</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
        }
    }

    $stmt->close();
}

?>

<?php require __DIR__ . "/../../Components/layout.php"; ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8 col-sm-12">
            <div class="card shadow-sm border p-4">
                <h2 class="mb-4 text-center">Create Account</h2>

                <?php echo $message; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="number" name="phone" class="form-control" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required />
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <button type="submit" class="btn btn-dark">Submit</button>
                        <a class="icon-link icon-link-hover text-decoration-none" href="<?php echo AUTH_URL; ?>/user/login">
                            <svg xmlns="http://www.w3.org/2000/svg" class="bi" viewBox="0 0 16 16" width="16" height="16" aria-hidden="true">
                                <path d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z" />
                            </svg>
                            Click here to Log In
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . "/../../Components/footer.php"; ?>
</body>

</html>