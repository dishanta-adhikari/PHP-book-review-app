<?php

abstract class DB
{

    protected $host = "localhost";
    protected $user = "root";
    protected $password = "";
    protected $database = "BRS_db";
    protected $conn;


    public function connect()
    {
        $this->conn = new mysqli($this->host, $this->user, $this->password, $this->database);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }

        // echo "Connected successfully";
    }
}
