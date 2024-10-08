<?php
// MySQL se connection
include 'conn.php'; // Maaloom ho ki conn.php mein $conn hai (MySQLi ya PDO connection)

// URL mein agar `id` parameter nahi hai toh error dikhayein
if (!isset($_GET['id'])) {
    echo "<div class='alert alert-danger'>Invalid post ID.</div>";
    exit;
}

$postId = (int)$_GET['id'];

// Specific blog post ko `id` ke base par fetch karo
$postQuery = "SELECT * FROM blog WHERE id = $postId AND status='active'";
$postResult = $conn->query($postQuery);

// Agar post exist karta hai toh usko show karo, warna error dikhao
if ($postResult && $postResult->num_rows > 0) {
    $post = $postResult->fetch_assoc();
} else {
    echo "<div class='alert alert-danger'>Blog post not found.</div>";
    exit;
}

// Dusre active blog posts ko sidebar ke liye fetch karo
$otherPostsQuery = "SELECT id, name FROM blog WHERE status='active' AND id != $postId LIMIT 5";
$otherPostsResult = $conn->query($otherPostsQuery);
$otherPosts = $otherPostsResult ? $otherPostsResult->fetch_all(MYSQLI_ASSOC) : [];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Detailed blog post view">
    <meta name="keywords" content="Blog, Articles, Details, Technology">
    <title><?= htmlspecialchars($post['name']); ?> - Blog Detail</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="icons/fontawesome/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .blog-post {
            border: 1px solid #ccc;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            background-color: #f8f9fa;
        }
        .image-container {
            position: relative;
            overflow: hidden;
            margin-bottom: 15px;
        }
        .blog-post img {
            max-width: 100%;
            height: auto;
            object-fit: cover;
        }
        .sidebar {
            padding-left: 20px;
        }
        .sidebar a {
            text-decoration: none;
        }
        .sidebar .other-post {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</head>
<body>
   <?php include 'navbar.php'; ?>
   <div class="container my-4">
       <div class="row">
           <div class="col-md-8">
               <!-- Main Blog Post Details -->
               <div class="blog-post shadow-sm">
                   <h1><?= htmlspecialchars($post['name']); ?></h1>
                   <?php 
                       $created_at = isset($post['created_at']) ? date('F j, Y', strtotime($post['created_at'])) : 'Date not available';
                       $author = isset($post['author']) ? htmlspecialchars($post['author']) : 'Unknown author';
                   ?>
                   <small>Posted on <?= $created_at; ?> by <?= $author; ?></small>

                   <div class="image-container mt-3 mb-3">
                       <img src="../adminside/<?= htmlspecialchars($post['image']); ?>" alt="<?= htmlspecialchars($post['name']); ?>" class="img-fluid">
                   </div>
                   <p><?= nl2br(htmlspecialchars($post['description'])); ?></p>
               </div>
           </div>

           <!-- Sidebar with other posts -->
           <div class="col-md-4">
               <div class="sidebar">
                   <h3>Other Blog Posts</h3>
                   <?php if (count($otherPosts) > 0) { ?>
                       <?php foreach ($otherPosts as $otherPost) { ?>
                           <div class="other-post shadow-sm">
                               <a href="blog_detail.php?id=<?= $otherPost['id']; ?>">
                                   <?= htmlspecialchars($otherPost['name']); ?>
                               </a>
                           </div>
                       <?php } ?>
                   <?php } else { ?>
                       <p>No other posts available.</p>
                   <?php } ?>
               </div>
           </div>
       </div>
   </div>

   <?php include 'footer.php'; ?>
   <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
   <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
