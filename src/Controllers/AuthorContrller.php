<?php

namespace App\Controllers;

use App\Models\Author;
use Exception;

class AuthorController
{
    private $Author;

    public function __construct($db)
    {
        $this->Author = new Author($db);
    }
}
