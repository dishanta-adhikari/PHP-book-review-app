<?php

namespace App\Controllers;

use App\Models\Book;
use Exception;

class BookController
{
    private $Book;

    public function __construct($db)
    {
        $this->Book = new Book($db);
    }




    public function updateBook($post, $files)
    {
        try {
            $id = (int) $post['id'] ?? null;

            $data = [];

            if (!empty($post['name'])) {
                $data['name'] = trim($post['name']);
            }

            if (!empty($post['vol'])) {
                $data['vol'] = (int) $post['vol'];
            }

            if (!empty($files['img_new']['name'])) {
                $ext = pathinfo($files['img_new']['name'], PATHINFO_EXTENSION);
                $imgName = uniqid() . "." . $ext;
                move_uploaded_file($files['img_new']['tmp_name'], "uploads/" . $imgName);
                $data['img_new'] = $imgName;
            }

            if (!empty($files['pdf_new']['name'])) {
                $ext = pathinfo($files['pdf_new']['name'], PATHINFO_EXTENSION);
                $pdfName = uniqid() . "." . $ext;
                move_uploaded_file($files['pdf_new']['tmp_name'], "uploads/" . $pdfName);
                $data['pdf_new'] = $pdfName;
            }

            if (!$id || empty($data)) {
                throw new Exception("Invalid input or no changes to update.");
            }

            $this->Book->update($id, $data);

            $_SESSION['success'] = "Request Rejected!";
            header("Location: " . APP_URL . "/src/views/...");
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: " . APP_URL . "/src/views/...");
            exit;
        }
    }
}
