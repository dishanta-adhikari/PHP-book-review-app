<?php

namespace App\Models;

class Review
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getRecentReviewsByBookId($book_id, $limit)
    {
        $limit = 3;
        $stmt = $this->conn->prepare("SELECT user_name, rating, comment FROM reviews WHERE book_id = ? ORDER BY id DESC LIMIT ?");
        $stmt->bind_param("ii", $book_id, $limit);
        if ($stmt->execute()) {
            return $stmt->get_result();
        }
        return false;
    }
}
