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

    public function verifyAuthor()
    {
        if (isset($_SESSION['author_id'])) {
            header("Location: " . APP_URL . "/src/views/author/dashboard");
            exit;
        }
    }
}
