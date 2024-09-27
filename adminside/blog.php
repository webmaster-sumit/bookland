<?php
// Connection to MySQL
include 'conn.php'; // Assuming conn.php contains $conn (MySQLi or PDO connection)

// Handle form submission for adding or updating blog posts
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    $author = $_POST['author'];
    $comments = $_POST['comments'];
    $image = '';

    // Image upload handling
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = 'uploads/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    }

    // Add new blog or update existing one
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Update existing post
        $id = $_POST['id'];

        if ($image) {
            $query = "UPDATE blog SET name='$name', description='$description', image='$image', status='$status', author='$author', comments='$comments' WHERE id=$id";
        } else {
            $query = "UPDATE blog SET name='$name', description='$description', status='$status', author='$author', comments='$comments' WHERE id=$id";
        }
    } else {
        // Add new post
        $query = "INSERT INTO blog (name, image, description, status, author, comments) VALUES ('$name', '$image', '$description', '$status', '$author', '$comments')";
    }

    if ($conn->query($query)) {
        echo "<div class='alert alert-success'>Blog post saved!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error saving post: " . $conn->error . "</div>";
    }
}

// Handle deletion of blog post
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $query = "DELETE FROM blog WHERE id=$id";
    if ($conn->query($query)) {
        echo "<div class='alert alert-success'>Blog post deleted!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error deleting post: " . $conn->error . "</div>";
    }
}

// Change status
if (isset($_GET['change_status'])) {
    $id = $_GET['change_status'];
    $query = "SELECT status FROM blog WHERE id=$id";
    $result = $conn->query($query);
    $post = $result->fetch_assoc();
    $new_status = ($post['status'] == 'active') ? 'inactive' : 'active';

    $query = "UPDATE blog SET status='$new_status' WHERE id=$id";
    if ($conn->query($query)) {
        echo "<div class='alert alert-success'>Status updated!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error updating status: " . $conn->error . "</div>";
    }
}

// Fetch all blog posts
$query = "SELECT * FROM blog";
$posts = $conn->query($query)->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Blog Management</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Manage Blog Posts</h1>

    <!-- Form to Add / Edit Blog Post -->
    <form action="blog.php" method="post" enctype="multipart/form-data" class="mb-4">
        <input type="hidden" name="id" value="<?= isset($_GET['edit']) ? $_GET['edit'] : ''; ?>">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="image">Image:</label>
            <input type="file" name="image" id="image" class="form-control-file">
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea name="description" id="description" class="form-control" required></textarea>
        </div>
        <div class="form-group">
            <label for="author">Author:</label>
            <input type="text" name="author" id="author" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="comments">Comments:</label>
            <textarea name="comments" id="comments" class="form-control"></textarea>
        </div>
        <div class="form-group">
            <label for="status">Status:</label>
            <select name="status" id="status" class="form-control">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
    </form>

    <hr>

    <!-- Display All Blog Posts -->
    <h2>All Blog Posts</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Image</th>
                <th>Description</th>
                <th>Author</th>
                <th>Comments</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($posts as $post) { ?>
                <tr>
                    <td><?= htmlspecialchars($post['name']); ?></td>
                    <td><img src="<?= htmlspecialchars($post['image']); ?>" alt="" width="100"></td>
                    <td><?= htmlspecialchars($post['description']); ?></td>
                    <td><?= htmlspecialchars($post['author']); ?></td>
                    <td><?= htmlspecialchars($post['comments']); ?></td>
                    <td><?= date('Y-m-d', strtotime($post['date'])); ?></td>
                    <td><?= htmlspecialchars($post['status']); ?></td>
                    <td>
                        <a href="blog.php?edit=<?= $post['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="blog.php?delete=<?= $post['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                        <a href="blog.php?change_status=<?= $post['id']; ?>" class="btn btn-info btn-sm">Toggle Status</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
