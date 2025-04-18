<?php
session_start();
include 'db.php';

if (isset($_GET['id'])) {
    $bookId = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
    $stmt->bind_param("i", $bookId);
    $stmt->execute();
    header("Location: view_books.php");
}
?>