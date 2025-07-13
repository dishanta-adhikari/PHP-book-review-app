<?php

namespace App\Models;

use Exception;

class Review
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create(array $values)
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO reviews (book_id, user_name, rating, comment) 
            VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("isis", $values['book_id'], $values['user_name'], $values['rating'], $values['comment']);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }


    public function getRecentReviewsByBookId(int $value) //$book_id
    {
        $limit = 3;
        $stmt = $this->conn->prepare("SELECT * FROM reviews WHERE book_id = ? ORDER BY id DESC LIMIT ?");
        $stmt->bind_param("ii", $value, $limit);
        if ($stmt->execute()) {
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        return false;
    }
}
