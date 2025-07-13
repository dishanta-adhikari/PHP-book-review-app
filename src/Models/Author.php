<?php

namespace App\Models;

class Author
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create(array $values)    //$name, $email, $phone, $address, $password
    {
        $stmt = $this->conn->prepare("
            INSERT INTO authors (name, email, phone, address, password, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->bind_param("ssiss", $values['name'], $values['email'], $values['phone'], $values['address'], $values['password']);
        if ($stmt->execute()) {
            $id = $this->conn->insert_id;
            return [
                'id' => $id,
                'name' => $values['name'],
                'email' => $values['email']
            ];
        }
        return false;
    }

    public function getAuthorById(int $author_id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM authors WHERE id = ?");
        $stmt->bind_param("i", $author_id);
        if ($stmt->execute()) {
            return $stmt->get_result()->fetch_assoc();
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
