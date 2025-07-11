<?php

namespace App\Models;

class User
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getUserByEmail(string $email)
    {
        $stmt = $this->conn->prepare("SELECT id,name,password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        if ($stmt->execute()) {
            return $stmt->get_result()->fetch_assoc();
        }
        return false;
    }
}
