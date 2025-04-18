<?php
session_start();
include 'db.php';

if (isset($_GET['id'])) {
    $bookId = $_GET['id'];
    $stmt = $conn->prepare("SELECT books.*, authors.name AS author_name, categories.name AS category_name FROM books LEFT JOIN authors ON books.author_id = authors.id LEFT JOIN categories ON books.category_id = categories.id WHERE books.id = ?");
    $stmt->bind_param("i", $bookId);
    $stmt->execute();
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();

    if ($book) {
        echo "<h5>Title: " . $book['title'] . "</h5>";
        echo "<p><strong>Author:</strong> " . $book['author_name'] . "</p>";
        echo "<p><strong>Category:</strong> " . $book['category_name'] . "</p>";
        echo "<p><strong>Price:</strong> $" . $book['price'] . "</p>";
        echo "<p><strong>Description:</strong> " . $book['description'] . "</p>"; // Assuming you have a description field
    } else {
        echo "No details found for this book.";
    }
}
?>