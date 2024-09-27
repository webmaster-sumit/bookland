<?php
// Include the database connection
include 'conn.php';

// Fetch all blog posts
$result = $conn->query("SELECT * FROM blog ORDER BY created_at DESC");
$blogs = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Blog Posts</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-5">All Blog Posts</h1>

        <table class="table table-bordered table-hover">
            <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Author</th>
                <th>Status</th>
                <th>Image</th>
                <th>Content</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($blogs as $blog): ?>
                <tr>
                    <td><?php echo htmlspecialchars($blog['id']); ?></td>
                    <td><?php echo htmlspecialchars($blog['title']); ?></td>
                    <td><?php echo htmlspecialchars($blog['author']); ?></td>
                    <td><?php echo htmlspecialchars($blog['status']); ?></td>
                    <td>
                        <?php if (!empty($blog['image'])): ?>
                            <img src="uploads/<?php echo htmlspecialchars($blog['image']); ?>" alt="Blog Image" style="max-width: 100px;">
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($blog['content']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <a href="admin_blog.php" class="btn btn-primary">Back to Admin</a>
    </div>
</body>
</html>
