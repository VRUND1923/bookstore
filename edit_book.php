<?php
session_start();
include 'db.php';

if (isset($_GET['id'])) {
    $bookId = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->bind_param("i", $bookId);
    $stmt->execute();
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $author_id = $_POST['author_id'];
    $category_id = $_POST['category_id'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    // Check if a new cover image is uploaded
    if ($_FILES['cover_image']['name']) {
        $cover_image = $_FILES['cover_image']['name'];
        move_uploaded_file($_FILES['cover_image']['tmp_name'], "uploads/" . $cover_image);
        $stmt = $conn->prepare("UPDATE books SET title=?, author_id=?, category_id=?, cover_image=?, price=?, description=? WHERE id=?");
        $stmt->bind_param("siisssi", $title, $author_id, $category_id, $cover_image, $price, $description, $bookId);
    } else {
        $stmt = $conn->prepare("UPDATE books SET title=?, author_id=?, category_id=?, price=?, description=? WHERE id=?");
        $stmt->bind_param("siissi", $title, $author_id, $category_id, $price, $description, $bookId);
    }
    $stmt->execute();
    header("Location: view_books.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Book</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Edit Book</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Title</label>
            <input type="text" name="title" class="form-control" value="<?php echo $book['title']; ?>" required>
        </div>
        <div class="form-group">
            <label>Author</label>
            <select name="author_id" class="form-control" required>
                <?php
                $authors = $conn->query("SELECT * FROM authors");
                while ($author = $authors->fetch_assoc()) {
                    $selected = ($author['id'] == $book['author_id']) ? 'selected' : '';
                    echo "<option value='" . $author['id'] . "' $selected>" . $author['name'] . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label>Category</label>
            <select name="category_id" class="form-control" required>
                <?php
                $categories = $conn->query("SELECT * FROM categories");
                while ($category = $categories->fetch_assoc()) {
                  $selected = ($category['id'] == $book['category_id']) ? 'selected' : '';
                  echo "<option value='" . $category['id'] . "' $selected>" . $category['name'] . "</option>";
              }
              ?>
          </select>
      </div>
      <div class="form-group">
          <label>Price</label>
          <input type="number" name="price" class="form-control" step="0.01" value="<?php echo $book['price']; ?>" required>
      </div>
      <div class="form-group">
          <label>Description</label>
          <textarea name="description" class="form-control" rows="4" required><?php echo $book['description']; ?></textarea>
      </div>
      <div class="form-group">
          <label>Cover Image</label>
          <input type="file" name="cover_image" class="form-control-file">
          <small class="form-text text-muted">Leave blank to keep the current image.</small>
      </div>
      <button type="submit" class="btn btn-primary btn-block">Update Book</button>
  </form>
  <p class="text-center mt-3"><a href="view_books.php">Back to Books List</a></p>
</div>
</body>
</html>