<?php
// Include the database connection
include 'conn.php'; // Ensure this file is correct and accessible

// Initialize message
$message = '';

// Handle add or update blog post
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $author = $_POST['author'];
    $status = $_POST['status'];
    $image = null;

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $image = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image;
        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $message = "Failed to upload image.";
        }
    }

    // Insert or update blog post
    if (isset($_POST['id']) && $_POST['id'] != '') {
        // Update blog post
        $id = $_POST['id'];
        $query = "UPDATE blog SET title=?, content=?, author=?, status=?, image=? WHERE id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('sssssi', $title, $content, $author, $status, $image, $id);
    } else {
        // Insert new blog post
        $query = "INSERT INTO blog (title, content, author, status, image) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('sssss', $title, $content, $author, $status, $image);
    }
    $stmt->execute();

    // Redirect to view_blog.php after submission
    header('Location: view_blog.php');
    exit(); // Always call exit after redirection
}

// Handle delete blog post
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $query = "DELETE FROM blog WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $message = "Blog post deleted successfully.";
}

// Fetch all blog posts for displaying in the admin area
$result = $conn->query("SELECT * FROM blog ORDER BY id DESC");
$blogs = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Blog Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            background-color: #f4f6f9;
        }
        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0px 3px 15px rgba(0, 0, 0, 0.1);
        }
        .btn-custom {
            background-color: #17a2b8;
            color: #fff;
        }
        .btn-custom:hover {
            background-color: #138496;
        }
        .form-control:focus {
            border-color: #17a2b8;
            box-shadow: 0 0 8px rgba(23, 162, 184, 0.2);
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .preview-image {
            margin-top: 10px;
            max-width: 200px;
            height: auto;
            border-radius: 4px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <?php include 'nav.php' ?>
    <div class="container mt-5">
        <h1 class="text-center mb-5">Blog Management</h1>

        <?php if ($message): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>

        <!-- Blog Table -->
        <h2>All Blog Posts</h2>
        <table class="table table-bordered table-hover">
            <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Author</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($blogs as $blog): ?>
                <tr>
                    <td><?php echo htmlspecialchars($blog['id']); ?></td>
                    <td><?php echo htmlspecialchars($blog['name']); ?></td>
                    <td><?php echo htmlspecialchars($blog['author']); ?></td>
                    <td><?php echo htmlspecialchars($blog['status']); ?></td>
                    <td>
                        <a href="admin_blog.php?edit_id=<?php echo $blog['id']; ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Edit</a>
                        <a href="admin_blog.php?delete_id=<?php echo $blog['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this post?');"><i class="fas fa-trash"></i> Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Blog Form -->
        <?php
        $edit_blog = null;
        if (isset($_GET['edit_id'])) {
            $id = $_GET['edit_id'];
            $result = $conn->query("SELECT * FROM blog WHERE id = $id");
            $edit_blog = $result->fetch_assoc();
        }
        ?>
        <h2 class="mt-5"><?php echo $edit_blog ? 'Edit' : 'Add'; ?> Blog Post</h2>
        <form action="admin_blog.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $edit_blog['id'] ?? ''; ?>">

            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" name="title" id="title" class="form-control" required value="<?php echo $edit_blog['title'] ?? ''; ?>">
            </div>

            <div class="form-group">
                <label for="content">Content:</label>
                <textarea name="content" id="content" class="form-control" rows="5" required><?php echo $edit_blog['content'] ?? ''; ?></textarea>
            </div>

            <div class="form-group">
                <label for="author">Author:</label>
                <input type="text" name="author" id="author" class="form-control" required value="<?php echo $edit_blog['author'] ?? ''; ?>">
            </div>

            <div class="form-group">
                <label for="status">Status:</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="active" <?php echo isset($edit_blog['status']) && $edit_blog['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo isset($edit_blog['status']) && $edit_blog['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>

            <div class="form-group">
                <label for="image">Image:</label>
                <input type="file" name="image" id="image" class="form-control" onchange="previewImage(event)">
                <?php if (!empty($edit_blog['image'])): ?>
                    <img src="uploads/<?php echo $edit_blog['image']; ?>" alt="Blog Image" class="preview-image">
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-custom"><?php echo $edit_blog ? 'Update' : 'Add'; ?> Post</button>
        </form>
    </div>

    <!-- JavaScript for live image preview -->
    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById('image-preview');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</body>
</html>
