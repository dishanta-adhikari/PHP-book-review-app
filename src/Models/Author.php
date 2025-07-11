<?php

namespace App\Models;

use PhpParser\Node\Expr\Cast\Array_;
use PhpParser\Node\Expr\Cast\String_;

class Author
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAuthorById(int $author_id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM authors WHERE id = ?");
        $stmt->bind_param("i", $author_id);
        if ($stmt->execute()) {
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        return false;
    }

    public function getAuthorByEmail(string $email)
    {
        $stmt = $this->conn->prepare("SELECT id,name,password FROM authors WHERE email = ?");
        $stmt->bind_param("s", $email);
        if ($stmt->execute()) {
            return $stmt->get_result()->fetch_assoc();
        }
        return false;
    }
}
