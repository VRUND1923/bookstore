<?php
session_start();
include 'db.php'; // Include your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $title = $_POST['title'];
    $author_id = $_POST['author_id'];
    $category_id = $_POST['category_id'];
    $cover_image = $_FILES['cover_image']['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    // Move the uploaded file to the desired directory
    move_uploaded_file($_FILES['cover_image']['tmp_name'], "uploads/" . $cover_image);

    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO books (title, author_id, category_id, cover_image, price, description) VALUES (?, ?, ?, ?, ?, ?)");

    // Bind parameters
    // Here, we are binding 6 variables: title (string), author_id (integer), category_id (integer), cover_image (string), price (decimal), description (string)
    $stmt->bind_param("siisss", $title, $author_id, $category_id, $cover_image, $price, $description);

    // Execute the statement
    if ($stmt->execute()) {
        // Redirect or show success message
        header("Location: view_books.php");
        exit();
    } else {
        echo "Error: " . $stmt->error; // Show error if execution fails
    }

    // Close the statement
    $stmt->close();
}

// Fetch authors and categories for the form (optional)
$authors = $conn->query("SELECT * FROM authors");
$categories = $conn->query("SELECT * FROM categories");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Book</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="text-center text-primary">Add New Book</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Author</label>
            <select name="author_id" class="form-control" required>
                <?php while ($author = $authors->fetch_assoc()): ?>
                    <option value="<?php echo $author['id']; ?>"><?php echo $author['name']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Category</label>
            <select name="category_id" class="form-control" required>
                <?php while ($category = $categories->fetch_assoc()): ?>
                    <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Price</label>
            <input type="number" name="price" class="form-control" step="0.01" required>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="4" required></textarea>
        </div>
        <div class="form-group">
            <label>Cover Image</label>
            <input type="file" name="cover_image" class="form-control-file" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Add Book</button>
    </form>
    <p class="text-center mt-3"><a href="view_books.php">Back to Books List</a></p>
</div>
</body>
</html>