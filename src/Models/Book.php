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

    public function getBookById(int $id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM books WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            return $stmt->get_result()->fetch_assoc();
        }
        return false;
    }

    public function getBooksByAuthorId(int $author_id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM books WHERE author_id = ?");
        $stmt->bind_param("i", $author_id);
        if ($stmt->execute()) {
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        return false;
    }

    public function getBooksWithAuthors(int $offset)  // $offset
    {
        $limit = 6;
        $stmt = $this->conn->prepare("SELECT books.*, authors.name AS author_name 
                FROM books 
                LEFT JOIN authors ON books.author_id = authors.id 
                ORDER BY books.id DESC 
                LIMIT ? OFFSET ?");
        $stmt->bind_param("ii", $limit, $offset);
        if ($stmt->execute()) {
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        return false;
    }

    public function getAllBooks(int $value)
    {
        $limit = 6;
        $stmt = $this->conn->prepare("SELECT books.id, books.name AS book_name, books.vol, books.img, books.pdf, authors.name AS author_name
            FROM books
            JOIN authors ON books.author_id = authors.id
            ORDER BY books.created_at DESC
            LIMIT ? OFFSET ?");
        $stmt->bind_param('ii', $limit, $value);
        if ($stmt->execute()) {
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
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

        $stmt = $this->conn->prepare("UPDATE books SET " . implode(", ", $fields) . " WHERE id = ?");
        $stmt->bind_param($types, ...$params);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function verifyOwner($book_id, $author_id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM books WHERE id = ? AND author_id = ? ");
        $stmt->bind_param("ii", $book_id, $author_id);
        if ($stmt->execute()) {
            return $stmt->get_result();
        }
        return false;
    }

    public function totalPages($limit)
    {
        $total_books_result = $this->conn->query("SELECT COUNT(*) AS total FROM books");
        $total_books = ($total_books_result) ? $total_books_result->fetch_assoc()['total'] : 0;
        $total_pages = ceil($total_books / $limit);
        return $total_pages;
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
