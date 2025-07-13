<?php

namespace App\Controllers;

use App\Models\Book;
use App\Models\Author;
use Exception;

class BookController
{
    private $Book, $Author;

    public function __construct($db)
    {
        $this->Book = new Book($db);
        $this->Author = new Author($db);
    }

    public function create($POST, $FILES)
    {
        try {
            if (
                empty($POST['name']) ||
                empty($POST['vol']) ||
                empty($FILES['cover']['name']) ||
                empty($FILES['pdf']['name'])
            ) {
                throw new Exception("All fields including cover image and PDF are required.");
            }

            $book_name = trim($POST['name']);
            $book_vol = (int) $POST['vol'];
            $author_id = $_SESSION['author_id'] ?? null;

            if (!$author_id) {
                throw new Exception("Unauthorized access.");
            }

            // Process cover image
            $img_ext = strtolower(pathinfo($FILES['cover']['name'], PATHINFO_EXTENSION));
            $allowed_img = ['jpg', 'jpeg', 'png'];
            if (!in_array($img_ext, $allowed_img)) {
                throw new Exception("Invalid image file type.");
            }

            $img_tmp = $FILES['cover']['tmp_name'];
            $img_new = date("YmdHis") . "." . $img_ext;
            $img_dest = __DIR__ . "/../../public/assets/uploads/" . $img_new;
            if (!is_dir(dirname($img_dest))) {
                mkdir(dirname($img_dest), 0777, true);
            }
            move_uploaded_file($img_tmp, $img_dest);

            // Process PDF
            $pdf_ext = strtolower(pathinfo($FILES['pdf']['name'], PATHINFO_EXTENSION));
            if ($pdf_ext !== "pdf") {
                throw new Exception("Invalid PDF file type.");
            }

            $pdf_tmp = $FILES['pdf']['tmp_name'];
            $pdf_new = date("YmdHis") . ".pdf";
            $pdf_dest = __DIR__ . "/../../public/assets/uploads/" . $pdf_new;
            if (!is_dir(dirname($pdf_dest))) {
                mkdir(dirname($pdf_dest), 0777, true);
            }
            move_uploaded_file($pdf_tmp, $pdf_dest);

            // Prepare values for DB insert
            $values = [
                'name' => $book_name,
                'vol' => $book_vol,
                'author_id' => $author_id,
                'img_new' => $img_new,
                'pdf_new' => $pdf_new
            ];

            // Call the model to insert
            $result = $this->Book->create($values);

            if (!$result) {
                throw new Exception("Failed to add book. Please try again.");
            }

            $_SESSION['success'] = "Book added successfully!";
            header("Location: " . APP_URL . "/src/views/author/dashboard");
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: " . APP_URL . "/src/views/book/create");
            exit;
        }
    }

    public function getAllBooks(int $value): array
    {
        return $this->Book->getAllBooks($value);
    }

    public function getBookById(int $id)
    {
        try {
            if (empty($id)) {
                throw new Exception("Book ID not found or unauthorized!");
            }

            $book_id = $id;
            $result = $this->Book->getBookById($book_id);

            if (!$result) {
                throw new Exception("Book Not Found. Invalid ID!");
            }

            return $result;
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location:" . APP_URL . "/src/views/author/dashboard");
            exit;
        }
    }

    public function getBooksWithAuthors(int $values)
    {
        return $this->Book->getBooksWithAuthors($values);
    }

    public function getBooksByAuthorId(int $author_id)
    {
        return $this->Book->getBooksByAuthorId($author_id);
    }

    public function update($post, $files)
    {
        try {
            if (empty($_GET['id'])) {
                throw new Exception("Unauthorized!");
            }

            $book_id = (int) $_GET['id'];
            $author_id = $_SESSION['author_id'];

            $verifyOwner = $this->Book->verifyOwner($book_id, $author_id);

            if ($verifyOwner->num_rows === 0) {
                throw new Exception("You are not authorized to edit this book.");
            }

            $data = [];

            if (!empty($post['name'])) {
                $data['name'] = trim($post['name']);
            }

            if (!empty($post['vol'])) {
                $data['vol'] = (int) $post['vol'];
            }

            if (!empty($files['cover']['name'])) {
                $ext = pathinfo($files['cover']['name'], PATHINFO_EXTENSION);
                $imgName = date("YmdHis") . "." . $ext;
                move_uploaded_file($files['cover']['tmp_name'], __DIR__ . "/../../public/assets/uploads/" . $imgName);
                $data['img_new'] = $imgName;
            }

            if (!empty($files['pdf']['name'])) {
                $ext = pathinfo($files['pdf']['name'], PATHINFO_EXTENSION);
                $pdfName = date("YmdHis") . "." . $ext;
                move_uploaded_file($files['pdf']['tmp_name'], __DIR__ . "/../../public/assets/uploads/" . $pdfName);
                $data['pdf_new'] = $pdfName;
            }

            if (!$book_id || empty($data)) {
                throw new Exception("Invalid input or no changes to update.");
            }

            $result = $this->Book->update($book_id, $data);

            if (!$result) {
                throw new Exception("Failed to update the book!");
            }

            $_SESSION['success'] = "Book has been updated !";
            header("Location: " . APP_URL . "/src/views/author/dashboard");
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: " . APP_URL . "/src/views/book/edit?id={$book_id}");
            exit;
        }
    }

    public function totalPages($limit)
    {
        return $this->Book->totalPages($limit);
    }

    public function delete(array $values)
    {
        try {
            $author_id =  $values['author_id'];
            $book_id =  $values['book_id'];
            $author_password = trim($values['password']);

            if (empty($author_id) || empty($book_id) || empty($author_password)) {
                throw new Exception("Required fields are empty!");
            }

            $author = $this->Author->getAuthorById($author_id);     //retrive the author

            if (!$author) {
                throw new Exception("No account found with this id.");
            }

            if (!password_verify($author_password, $author['password'])) {
                throw new Exception("Invalid Password!");
            }

            $delete = $this->Book->delete($book_id);

            if (!$delete) {
                throw new Exception("Failed to delete the book!");
            }

            $_SESSION['success'] = "Book is deleted!";
            header("Location:" . APP_URL . "/src/views/author/dashboard");
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location:" . APP_URL . "/src/views/author/dashboard");
            exit;
        }
    }
}
