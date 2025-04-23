<?php
session_start();
include 'db.php'; // Include your database connection file

// FTP credentials
$ftp_server = "ftpupload.net";
$ftp_user = "if0_38768111";
$ftp_pass = "f7emjnvo9XM5U";
$ftp_dir = "/htdocs/uploads/";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $author_name = $_POST['author_name']; // Changed to author_name
    $category_id = $_POST['category_id'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    // Handle image upload via FTP
    $cover_image = $_FILES['cover_image']['name'];
    $tmp_name = $_FILES['cover_image']['tmp_name'];
    $ext = pathinfo($cover_image, PATHINFO_EXTENSION);
    $filename = uniqid("book_", true) . "." . $ext;
    $ftp_path = $ftp_dir . $filename;

    $ftp_conn = ftp_connect($ftp_server);
    $upload_success = false;

    if ($ftp_conn && ftp_login($ftp_conn, $ftp_user, $ftp_pass)) {
        ftp_pasv($ftp_conn, true);
        if (ftp_put($ftp_conn, $ftp_path, $tmp_name, FTP_BINARY)) {
            $upload_success = true;
        }
        ftp_close($ftp_conn);
    }

    if ($upload_success) {
        // Save only the filename in the DB
        $stmt = $conn->prepare("INSERT INTO books (title, author_name, category_id, cover_image, price, description) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiiss", $title, $author_name, $category_id, $filename, $price, $description);

        if ($stmt->execute()) {
            header("Location: view_books.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "<div style='color:red;'>Failed to upload image to FTP server.</div>";
    }
}

// Fetch categories (you can also hardcode them if you prefer)
$categories = [
    1 => "Fiction",
    2 => "Non-Fiction",
    3 => "Science",
    4 => "Biography",
    5 => "Fantasy"
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Book</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        h1, h2 {
            color: #343a40;
        }
        .navbar {
            margin-bottom: 20px;
        }
        .table {
            margin-top: 20px;
        }
        .btn {
            margin-top: 10px;
        }
    </style>
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
            <label>Author Name</label>
            <input type="text" name="author_name" class="form-control" required> <!-- Changed to text input -->
        </div>
        <div class="form-group">
            <label>Category</label>
            <select name="category_id" class="form-control" required>
                <?php foreach ($categories as $id => $name): ?>
                    <option value="<?php echo $id; ?>"><?php echo $name; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Price</label>
            <input type="number" name="price" class="form-control" step="0.01" required>
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