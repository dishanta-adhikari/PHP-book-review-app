<?php
require_once __DIR__ . "/../../Components/layout.php";

use App\Middleware\SessionMiddleware;
use App\Controllers\LoginController;
use App\Helpers\Flash;

SessionMiddleware::verifySession('user');       //it redirects the user to the dashboard if user is already logged in 
SessionMiddleware::verifySession('author');     //it redirects the author to the dashboard if user is already logged in 

$login = new LoginController($conn);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $login->login($_POST);      //log the user in
}

?>

<body class="bg-light">
    <main class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 col-sm-10">
                <div class="card shadow border rounded p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="mb-0">Log In</h2>
                        <a href="<?= AUTH_URL ?>/author/login"
                            class="icon-link icon-link-hover link-body-emphasis link-offset-2 link-underline-opacity-25 link-underline-opacity-75-hover small">
                            Login as Author
                        </a>
                    </div>

                    <?php Flash::render(); ?> <!-- render the session message -->

                    <form method="POST" class="mt-3">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" required />
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" id="password" class="form-control" required />
                        </div>
                        <input type="hidden" name="role" id="role" class="form-control" value="user" required />
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="submit" class="btn btn-dark">Login</button>
                            <a href="<?= AUTH_URL ?>/user/register"
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