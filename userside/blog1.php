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
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="icons/fontawesome/css/all.min.css">
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
                    <h2>
                        <a href="blog_detail.php?id=<?= $post['id']; ?>">
                            <?= htmlspecialchars($post['name']); ?>
                        </a>
                    </h2>

                    <?php 
                        $created_at = isset($post['created_at']) ? date('F j, Y', strtotime($post['created_at'])) : 'Date not available';
                        $author = isset($post['author']) ? htmlspecialchars($post['author']) : 'Unknown author';
                    ?>
                    <small>Posted on <?= $created_at; ?> by <?= $author; ?></small>

                    <div class="image-container mt-3 mb-3">
                        <a href="blog_detail.php?id=<?= $post['id']; ?>">
                            <img src="../adminside/<?= htmlspecialchars($post['image']); ?>" alt="<?= htmlspecialchars($post['name']); ?>" class="img-fluid">
                        </a>
                    </div>
                    <p><?= nl2br(htmlspecialchars($post['description'])); ?></p>
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

    <?php include 'footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
