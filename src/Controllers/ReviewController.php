<?php

namespace App\Controllers;

use App\Models\Review;

class ReviewController
{
    private $Review;

    public function __construct($db)
    {
        $this->Review = new Review($db);
    }
}
