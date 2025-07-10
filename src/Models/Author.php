<?php

namespace App\Models;

class Author
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAuthorById($author_id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM authors WHERE id = ?");
        $stmt->bind_param("i", $author_id);
        if ($stmt->execute()) {
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        return false;
    }

    public function getAuthorByEmail($email)
    {
        $stmt = $this->conn->prepare("SELECT * FROM authors WHERE email = ?");
        $stmt->bind_param("s", $email);
        if ($stmt->execute()) {
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        return false;
    }
}
