<?php

namespace App\Controllers;

use App\Models\User;
use Exception;

class UserController
{
    private $User;

    public function __construct($db)
    {
        $this->User = new User($db);
    }

    public function verifyUser()
    {
        if (isset($_SESSION['user_id'])) {
            header("Location: " . APP_URL . "/src/views/user/dashboard");
            exit;
        }
    }
}
