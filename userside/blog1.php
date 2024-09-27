<?php
// Connection to MySQL
include 'conn.php'; // Assuming conn.php contains $conn (MySQLi or PDO connection)

// Initialize the $posts variable as an empty array to avoid undefined variable error
$posts = [];
$limit = 5; // Posts per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Fetch all active blog posts with pagination
$query = "SELECT * FROM blog WHERE status='active' LIMIT $start, $limit";
$result = $conn->query($query);

// Check if the query was successful and returned results
if ($result) {
    $posts = $result->fetch_all(MYSQLI_ASSOC);
} else {
    echo "<div class='alert alert-danger'>Error fetching posts: " . $conn->error . "</div>";
}

// Fetch total post count for pagination
$totalPostsQuery = "SELECT COUNT(id) AS total FROM blog WHERE status='active'";
$totalPostsResult = $conn->query($totalPostsQuery);
$totalPosts = $totalPostsResult->fetch_assoc()['total'];
$totalPages = ceil($totalPosts / $limit);

// Handle comments submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment'])) {
    $post_id = $_POST['post_id'];
    $author = $_POST['author'];
    $comment = $_POST['comment'];

    $stmt = $conn->prepare("INSERT INTO comments (post_id, author, comment) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $post_id, $author, $comment);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Comment added!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error adding comment: " . $conn->error . "</div>";
    }
}

// Fetch comments for each post
$comments = [];
foreach ($posts as $post) {
    $post_id = $post['id'];
    $stmt = $conn->prepare("SELECT * FROM comments WHERE post_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $comments[$post_id] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Read our latest blogs and share your thoughts.">
    <meta name="keywords" content="Blog, Articles, Technology, News">
    <title>Blog</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .blog-post {
            border: 1px solid #ccc;
            margin: 20px 0;
            border-radius: 8px;
            overflow: hidden;
            background-color: #f8f9fa;
        }
        .image-container {
            position: relative;
            overflow: hidden;
        }
        .blog-post img {
            max-width: 100%;
            height: 200px;
            object-fit: cover;
            transition: transform 0.3s ease-in-out;
        }
        .blog-post img:hover {
            transform: scale(1.1);
        }
        .footer {
            background-color: #343a40;
            color: white;
            padding: 20px 0;
            text-align: center;
        }
        @media (max-width: 768px) {
            .blog-post {
                margin: 10px 0;
            }
        }
    </style>
</head>
<body>
   <?php include 'navbar.php'; ?>

    <div class="container my-4">
        <h1 class="my-4">Blog Posts</h1>

        <!-- Check if there are posts -->
        <?php if (count($posts) > 0) { ?>
            <?php foreach ($posts as $post) { ?>
                <div class="blog-post p-3 shadow-sm">
                    <h2><?= htmlspecialchars($post['name']); ?></h2>

                    <?php 
                        // Use isset() to ensure 'created_at' exists in the array, provide a fallback if not
                        $created_at = isset($post['created_at']) ? date('F j, Y', strtotime($post['created_at'])) : 'Date not available';
                        $author = isset($post['author']) ? htmlspecialchars($post['author']) : 'Unknown author';
                    ?>
                    <small>Posted on <?= $created_at; ?> by <?= $author; ?></small>

                    <div class="image-container mt-3 mb-3">
                        <img src="../adminside/<?= htmlspecialchars($post['image']); ?>" alt="<?= htmlspecialchars($post['name']); ?>" class="img-fluid">
                    </div>
                    <p><?= nl2br(htmlspecialchars($post['description'])); ?></p>
                    
                    <!-- Comments Section -->
                    <h4>Comments:</h4>
                    <?php if (isset($comments[$post['id']]) && count($comments[$post['id']]) > 0) { ?>
                        <ul class="list-unstyled">
                            <?php foreach ($comments[$post['id']] as $comment) { ?>
                                <li class="mb-2">
                                    <strong><?= htmlspecialchars($comment['author']); ?></strong>: <?= nl2br(htmlspecialchars($comment['comment'])); ?>
                                    <br><small class="text-muted"><?= date('F j, Y, g:i a', strtotime($comment['created_at'])); ?></small>
                                </li>
                            <?php } ?>
                        </ul>
                    <?php } else { ?>
                        <p>No comments yet. Be the first to comment!</p>
                    <?php } ?>
                    
                    <!-- Comment Form -->
                    <form action="blog1.php" method="post" class="mt-3">
                        <input type="hidden" name="post_id" value="<?= $post['id']; ?>">
                        <div class="form-group">
                            <label for="author">Your Name:</label>
                            <input type="text" name="author" id="author" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="comment">Your Comment:</label>
                            <textarea name="comment" id="comment" class="form-control" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Comment</button>
                    </form>
                </div>
            <?php } ?>

            <!-- Pagination -->
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
                        <li class="page-item <?= ($i == $page) ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?= $i; ?>"><?= $i; ?></a>
                        </li>
                    <?php } ?>
                </ul>
            </nav>
        <?php } else { ?>
            <div class="alert alert-warning" role="alert">
                No blog posts available.
            </div>
        <?php } ?>
    </div>

    <!-- Footer -->
    <footer class="footer bg-dark text-white text-center py-4">
        <div class="container">
            <span>&copy; 2024 My Blog. All rights reserved. | <a href="#" class="text-white">Privacy Policy</a></span>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
