<?php

namespace App\Controllers;

use App\Models\Review;
use Exception;

class ReviewController
{
    private $Review;

    public function __construct($db)
    {
        $this->Review = new Review($db);
    }

    public function create(array $values)
    {
        try {
            if (empty($values["book_id"]) || empty($values["user_name"]) || empty(["rating"]) || empty($values["comment"])) {
                throw new Exception('Required Fields are Empty!');
            }

            $review = $this->Review->create($values);

            if (!$review) {
                throw new Exception('Something went wrong. Please try again!');
            }

            $_SESSION['success'] = 'Thank you for your valuable feedback ! hope you have a wonderful day.';
            header("Location: " . APP_URL);
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: " . APP_URL);
            exit;
        }
    }

    public function getRecentReviewsByBookId(int $values) //$book_id
    {
        try {
            if (empty($values)) {
                throw new Exception("Invalid Book ID!");
            }
            return $this->Review->getRecentReviewsByBookId($values);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
    }
}
