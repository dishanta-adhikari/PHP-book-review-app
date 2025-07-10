<?php

namespace App\Models;

class User
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }
}
