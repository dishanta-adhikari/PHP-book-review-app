<?php
require __DIR__ . "/../../App/app.php";
require __DIR__ . "/../../config/url.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $book_id = $_POST["book_id"];
    $user_name = $_POST["user_name"];
    $rating = $_POST["rating"];
    $comment = $_POST["comment"];

    $stmt = $conn->prepare("INSERT INTO reviews (book_id, user_name, rating, comment) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isis", $book_id, $user_name, $rating, $comment);

    if ($stmt->execute()) {
        echo "Review added";
    } else {
        echo "Failed: " . $conn->error;
    }
    $stmt->close();
}
