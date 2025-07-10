<?php

namespace App\Controllers;

use App\Models\User;

class LoginController
{
    private $User;

    public function __construct($db)
    {
        $this->User = new User($db);
    }
}
