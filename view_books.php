<?php
session_start();
include 'db.php';

// Fetch books from the database
$result = $conn->query("SELECT books.*, authors.name AS author_name, categories.name AS category_name FROM books LEFT JOIN authors ON books.author_id = authors.id LEFT JOIN categories ON books.category_id = categories.id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Books</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">Bookstore</a>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="add_book.php">Add Book</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">Logout</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container mt-5">
    <h2 class="text-center">Books List</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Title</th>
                <th>Author</th>
                <th>Category</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($book = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $book['title']; ?></td>
                    <td><?php echo $book['author_name']; ?></td>
                    <td><?php echo $book['category_name']; ?></td>
                    <td>$<?php echo $book['price']; ?></td>
                    <td>
                        <a href="edit_book.php?id=<?php echo $book['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="delete_book.php?id=<?php echo $book['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                        <button class="btn btn-info btn-sm" onclick="fetchBookDetails(<?php echo $book['id']; ?>)">View Details</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Modal for Book Details -->
<div class="modal fade" id="bookModal" tabindex="-1" role="dialog" aria-labelledby="bookModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
    <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bookModalLabel">Book Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="bookDetails">
                <!-- Book details will be loaded here via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function fetchBookDetails(bookId) {
    fetch('getBookDetails.php?id=' + bookId)
        .then(response => response.text())
        .then(data => {
            document.getElementById('bookDetails').innerHTML = data;
            $('#bookModal').modal('show');
        });
}
</script>

</body>
</html>