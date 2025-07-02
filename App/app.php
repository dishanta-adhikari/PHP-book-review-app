<?php
require __DIR__ . "/../config/config.php";


class App extends DB
{
    public function __construct()
    {
        $this->connect();
    }

    // CRUD operations for books
    // Create a new book
    public function createBook($name, $vol, $author_id, $img_new, $pdf_new)
    {
        $stmt = $this->conn->prepare("INSERT INTO books (name, vol, author_id, img, pdf) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("siiss", $name, $vol, $author_id, $img_new, $pdf_new);
        return $stmt->execute();
    }

    // Read/Get a book by ID
    public function getBookById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM books WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_assoc() : null;
    }

    // Read/Get all books by author
    public function getBooksByAuthor($author_id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM books WHERE author_id = ?");
        $stmt->bind_param("i", $author_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $books = [];
        while ($row = $result->fetch_assoc()) {
            $books[] = $row;
        }
        return $books;
    }

    public function getBooksCountByAuthor($author_id)
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM books WHERE author_id = ?");
        $stmt->bind_param("i", $author_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $row = $result->fetch_assoc()) {
            return (int)$row['count'];
        }
        return 0;
    }

    // Update a book
    public function updateBook($id, $name, $vol, $img_new = null, $pdf_new = null)
    {
        // Build dynamic query based on which files are updated
        $fields = [];
        $params = [];
        $types = "";

        if ($name !== null) {
            $fields[] = "name = ?";
            $params[] = $name;
            $types .= "s";
        }
        if ($vol !== null) {
            $fields[] = "vol = ?";
            $params[] = $vol;
            $types .= "i";
        }
        if ($img_new !== null) {
            $fields[] = "img = ?";
            $params[] = $img_new;
            $types .= "s";
        }
        if ($pdf_new !== null) {
            $fields[] = "pdf = ?";
            $params[] = $pdf_new;
            $types .= "s";
        }

        if (empty($fields)) {
            return false;
        }

        $params[] = $id;
        $types .= "i";

        $sql = "UPDATE books SET " . implode(", ", $fields) . " WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        return $stmt->execute();
    }

    // Delete a book
    public function deleteBook($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM books WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    
    // Get author by ID
    public function getAuthorById($author_id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM authors WHERE id = ?");
        $stmt->bind_param("i", $author_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Get author by email
    public function getAuthorByEmail($email)
    {
        $stmt = $this->conn->prepare("SELECT * FROM authors WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    // Get books with author info and pagination
    public function getBooksWithAuthors($limit, $offset)
    {
        $sql = "SELECT books.*, authors.name AS author_name 
                FROM books 
                LEFT JOIN authors ON books.author_id = authors.id 
                ORDER BY books.id DESC 
                LIMIT ? OFFSET ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    // Get recent reviews for a book (limit N)
    public function getRecentReviewsByBookId($book_id, $limit = 3)
    {
        $stmt = $this->conn->prepare("SELECT user_name, rating, comment FROM reviews WHERE book_id = ? ORDER BY id DESC LIMIT ?");
        $stmt->bind_param("ii", $book_id, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }
    
}
