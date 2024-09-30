<?php
session_start();
include 'conn.php';

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: adm_login.php");
    exit();
}

// Initialize message variable
$message = '';
$edit_mode = false; // Indicates if the form is in edit mode
$product_to_edit = null; // Stores the product data when in edit mode

// Handle product deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    
    // Fetch the image to delete it as well
    $stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->bind_result($image_path);
    $stmt->fetch();
    $stmt->close();
    
    // Delete product from database
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        // If the product has an image, delete it from the server
        if ($image_path && file_exists($image_path)) {
            unlink($image_path);
        }
        $message = "Product deleted successfully.";
    } else {
        $message = "Failed to delete product.";
    }
    $stmt->close();
}

// Handle product update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $product_id = $_POST['product_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $author_name = $_POST['author_name'];        // New field
    $publisher_name = $_POST['publisher_name'];  // New field
    $year = $_POST['year'];                      // New field
    $image_path = '';

    // Check if a new image was uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_name = $_FILES['image']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($file_ext, $allowed_exts)) {
            $image_path = 'uploads/' . uniqid() . '.' . $file_ext;
            if (move_uploaded_file($file_tmp, $image_path)) {
                // Fetch the old image to delete it
                $stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
                $stmt->bind_param("i", $product_id);
                $stmt->execute();
                $stmt->bind_result($old_image_path);
                $stmt->fetch();
                $stmt->close();

                // Delete the old image if a new one is uploaded
                if ($old_image_path && file_exists($old_image_path)) {
                    unlink($old_image_path);
                }
            } else {
                $message = "Failed to upload the new image.";
                $image_path = ''; // Reset image path on failure
            }
        } else {
            $message = "Invalid image format.";
            $image_path = ''; // Reset image path on failure
        }
    }

    // Update the product in the database
    if (!empty($name) && !empty($price)) {
        if ($image_path) {
            $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, description = ?, image = ?, author_name = ?, publisher_name = ?, year = ? WHERE id = ?");
            $stmt->bind_param("ssssssii", $name, $price, $description, $image_path, $author_name, $publisher_name, $year, $product_id);
        } else {
            $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, description = ?, author_name = ?, publisher_name = ?, year = ? WHERE id = ?");
            $stmt->bind_param("ssssssi", $name, $price, $description, $author_name, $publisher_name, $year, $product_id);
        }

        if ($stmt->execute()) {
            $message = "Product updated successfully.";
        } else {
            $message = "Failed to update product.";
        }
        $stmt->close();
    } else {
        $message = "Please fill out all required fields.";
    }
}

// Handle product addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $author_name = $_POST['author_name'];        // New field
    $publisher_name = $_POST['publisher_name'];  // New field
    $year = $_POST['year'];                      // New field
    $image_path = '';
     
    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_name = $_FILES['image']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($file_ext, $allowed_exts)) {
            $image_path = 'uploads/' . uniqid() . '.' . $file_ext;
            if (move_uploaded_file($file_tmp, $image_path)) {
                // Image successfully uploaded
            } else {
                $message = "Failed to upload image.";
                $image_path = ''; // Reset image path on failure
            }
        } else {
            $message = "Invalid image format.";
            $image_path = ''; // Reset image path on failure
        }
    }

    if (!empty($name) && !empty($price)) {
        $stmt = $conn->prepare("INSERT INTO products (name, price, description, image, author_name, publisher_name, year) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssi", $name, $price, $description, $image_path, $author_name, $publisher_name, $year);
        if ($stmt->execute()) {
            $message = "Product added successfully!";
        } else {
            $message = "Failed to add product.";
        }
        $stmt->close();
    } else {
        $message = "Please fill out all required fields.";
    }
}

// Fetch all products
$query = "SELECT * FROM products ORDER BY id DESC";
$result = $conn->query($query);

// Fetch product details if in edit mode
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $product_to_edit = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $edit_mode = true; // Set to edit mode
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .container {
            margin-top: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .img-thumbnail {
            max-width: 100px;
            max-height: 100px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>

    <div class="container">
        <h1>Product Management</h1>
        
        <?php if (!empty($message)): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>

        <!-- Add/Edit Product Form -->
        <div class="card mb-4">
            <div class="card-header"><?php echo $edit_mode ? 'Edit Product' : 'Add Product'; ?></div>
            <div class="card-body">
                <form action="product.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="product_id" value="<?php echo $edit_mode ? $product_to_edit['id'] : ''; ?>">
                    <div class="form-group">
                        <label for="name">Product Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo $edit_mode ? $product_to_edit['name'] : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="number" class="form-control" id="price" name="price" step="0.01" value="<?php echo $edit_mode ? $product_to_edit['price'] : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo $edit_mode ? $product_to_edit['description'] : ''; ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="author_name">Author Name</label>
                        <input type="text" class="form-control" id="author_name" name="author_name" value="<?php echo $edit_mode ? $product_to_edit['author_name'] : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="publisher_name">Publisher Name</label>
                        <input type="text" class="form-control" id="publisher_name" name="publisher_name" value="<?php echo $edit_mode ? $product_to_edit['publisher_name'] : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="year">Year</label>
                        <input type="number" class="form-control" id="year" name="year" value="<?php echo $edit_mode ? $product_to_edit['year'] : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="image">Product Image</label>
                        <input type="file" class="form-control-file" id="image" name="image">
                        <?php if ($edit_mode && $product_to_edit['image'] && file_exists($product_to_edit['image'])): ?>
                            <img src="<?php echo $product_to_edit['image']; ?>" alt="Product Image" class="img-thumbnail mt-2">
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="btn btn-primary" name="<?php echo $edit_mode ? 'update_product' : 'add_product'; ?>">
                        <?php echo $edit_mode ? 'Update Product' : 'Add Product'; ?>
                    </button>
                </form>
            </div>
        </div>

        <!-- Product List Table -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Description</th>
                    <th>Author</th>       <!-- New column -->
                    <th>Publisher</th>    <!-- New column -->
                    <th>Year</th>         <!-- New column -->
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['price']; ?></td>
                        <td><?php echo $row['description']; ?></td>
                        <td><?php echo $row['author_name']; ?></td>       <!-- New field -->
                        <td><?php echo $row['publisher_name']; ?></td>    <!-- New field -->
                        <td><?php echo $row['year']; ?></td>              <!-- New field -->
                        <td>
                            <?php if ($row['image'] && file_exists($row['image'])): ?>
                                <img src="<?php echo $row['image']; ?>" alt="Product Image" class="img-thumbnail">
                            <?php else: ?>
                                No Image
                            <?php endif; ?>
                        </td>
                        <td>
                            <!-- Update and Delete Actions -->
                            <a href="product.php?edit_id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                            <a href="product.php?delete_id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
