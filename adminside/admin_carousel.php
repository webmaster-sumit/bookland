<?php
session_start();
$target_dir = "uploads/";
$current_image = "current_image.jpg";
$message = "";

// Handle upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    
    // Check if image file is a real image
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check === false) {
        $message = "File is not an image.";
    } elseif (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        // Update current image reference
        if (file_exists($current_image)) {
            unlink($current_image); // Remove the old image
        }
        rename($target_file, $current_image); // Rename new image
        $message = "File uploaded successfully: " . htmlspecialchars(basename($_FILES["image"]["name"]));
    } else {
        $message = "Error uploading your file.";
    }

    // Redirect to adminside page after upload
    header("Location: ../adminside/index.php"); // Change this to your admin page
    exit();
}

// Handle delete
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    if (file_exists($current_image)) {
        unlink($current_image);
        $message = "Image deleted successfully.";
    } else {
        $message = "No image found to delete.";
    }

    // Redirect to adminside page after delete
    header("Location: ../adminside/index.php"); // Change this to your admin page
    exit();
}

// Handle view
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['view'])) {
    echo "<script>window.open('$current_image', '_blank');</script>";
    
    // Redirect after view action (optional)
    header("Location: ../adminside/index.php"); // Change this to your admin page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Image Management</title>
</head>
<body>
    <h1>Admin Image Management</h1>

    <?php if ($message) echo "<p>$message</p>"; ?>

    <form action="" method="post" enctype="multipart/form-data">
        <label for="image">Upload Image:</label>
        <input type="file" name="image" id="image" required>
        <button type="submit">Upload</button>
    </form>

    <h2>Current Image</h2>
    <?php if (file_exists($current_image)): ?>
        <img src="<?php echo $current_image; ?>" alt="Admin Image" style="width:200px;height:auto;">
        <form action="" method="post">
            <button type="submit" name="view">View Image</button>
        </form>
    <?php else: ?>
        <p>No image uploaded yet.</p>
    <?php endif; ?>

    <form action="" method="post">
        <button type="submit" name="delete">Delete Image</button>
    </form>
</body>
</html>
