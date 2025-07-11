<?php
require_once __DIR__ . "/../../Components/layout.php";

use App\Middleware\SessionMiddleware;
use App\Controllers\LoginController;
use App\Helpers\Flash;

SessionMiddleware::verifySession('author');     //it redirects the author to the dashboard if user is already logged in 
SessionMiddleware::verifySession('user');       //it redirects the user to the dashboard if user is already logged in 

$login = new LoginController($conn);
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $login->login($_POST); //log the author in
}

?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-10 col-md-8 col-lg-6">
            <div class="card shadow-sm border p-4">
                <h2 class="mb-4 text-center">Log In | Author</h2>

                <?php Flash::render(); ?> <!-- render the session message -->

                <form method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" required />
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required />
                    </div>
                    <input type="hidden" name="role" id="role" class="form-control" value="author" required />
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-dark">Login</button>
                    </div>

                    <div class="mt-3 text-center small">
                        <a href="<?= AUTH_URL ?>/author/register" class="d-block mb-1 icon-link icon-link-hover link-body-emphasis link-offset-2 link-underline-opacity-25 link-underline-opacity-75-hover">Don't have an account?</a>
                        <a href="<?= AUTH_URL ?>/user/login" class="d-block icon-link icon-link-hover link-body-emphasis link-offset-2 link-underline-opacity-25 link-underline-opacity-75-hover">Log in as User</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . "/../../Components/footer.php"; ?>
</body>

</html>