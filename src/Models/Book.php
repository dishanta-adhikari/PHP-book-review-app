<?php

namespace App\Models;

use Exception;

class Book
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create($values) //create new book
    {
        $stmt = $this->conn->prepare("INSERT INTO books (name, vol, author_id, img, pdf) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("siiss", $values['name'], $values['vol'], $values['author_id'], $values['img_new'], $values['pdf_new']);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getBookById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM books WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        return false;
    }

    public function getBooksByAuthorId($author_id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM books WHERE author_id = ?");
        $stmt->bind_param("i", $author_id);
        if ($stmt->execute()) {
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        return false;
    }

    public function getBooksWithAuthors($limit, $offset)
    {
        $stmt = $this->conn->prepare("SELECT books.*, authors.name AS author_name 
                FROM books 
                LEFT JOIN authors ON books.author_id = authors.id 
                ORDER BY books.id DESC 
                LIMIT ? OFFSET ?");
        $stmt->bind_param("ii", $limit, $offset);
        if ($stmt->execute()) {
            return $stmt->get_result();
        }
        return false;
    }

    public function update($id, $data)
    {
        $fields = [];
        $params = [];
        $types = "";

        if (isset($data['name'])) {
            $fields[] = "name = ?";
            $params[] = $data['name'];
            $types .= "s";
        }

        if (isset($data['vol'])) {
            $fields[] = "vol = ?";
            $params[] = $data['vol'];
            $types .= "i";
        }

        if (isset($data['img_new'])) {
            $fields[] = "img = ?";
            $params[] = $data['img_new'];
            $types .= "s";
        }

        if (isset($data['pdf_new'])) {
            $fields[] = "pdf = ?";
            $params[] = $data['pdf_new'];
            $types .= "s";
        }

        if (empty($fields)) {
            throw new Exception('No fields provided for update.');
        }

        $params[] = $id;
        $types .= "i";

        $sql = "UPDATE books SET " . implode(", ", $fields) . " WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM books WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
