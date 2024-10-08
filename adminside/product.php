<?php
session_start();
include 'conn.php'; // Database connection file

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: adm_login.php");
    exit();
}

// Initialize message variable
$message = '';
$edit_mode = false;
$product_to_edit = null;

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
    $author_name = $_POST['author_name'];
    $publisher_name = $_POST['publisher_name'];
    $year = $_POST['year'];
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
    $author_name = $_POST['author_name'];
    $publisher_name = $_POST['publisher_name'];
    $year = $_POST['year'];
    $image_path = '';

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_name = $_FILES['image']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($file_ext, $allowed_exts)) {
            $image_path = 'uploads/' . uniqid() . '.' . $file_ext;
            if (!move_uploaded_file($file_tmp, $image_path)) {
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

// Handle status toggle
if (isset($_GET['toggle_status_id'])) {
    $toggle_id = $_GET['toggle_status_id'];
    $stmt = $conn->prepare("SELECT status FROM products WHERE id = ?");
    $stmt->bind_param("i", $toggle_id);
    $stmt->execute();
    $stmt->bind_result($current_status);
    $stmt->fetch();
    $stmt->close();

    $new_status = ($current_status === 'active') ? 'inactive' : 'active';
    $stmt = $conn->prepare("UPDATE products SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $toggle_id);
    if ($stmt->execute()) {
        $message = "Product status updated to " . ucfirst($new_status) . ".";
    } else {
        $message = "Failed to update product status.";
    }
    $stmt->close();
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
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="product_id" value="<?php echo $edit_mode ? $product_to_edit['id'] : ''; ?>">
            <div class="form-group">
                <label for="name">Product Name</label>
                <input type="text" class="form-control" name="name" value="<?php echo $edit_mode ? $product_to_edit['name'] : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="price">Price</label>
                <input type="text" class="form-control" name="price" value="<?php echo $edit_mode ? $product_to_edit['price'] : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" name="description" required><?php echo $edit_mode ? $product_to_edit['description'] : ''; ?></textarea>
            </div>
            <div class="form-group">
                <label for="author_name">Author Name</label>
                <input type="text" class="form-control" name="author_name" value="<?php echo $edit_mode ? $product_to_edit['author_name'] : ''; ?>">
            </div>
            <div class="form-group">
                <label for="publisher_name">Publisher Name</label>
                <input type="text" class="form-control" name="publisher_name" value="<?php echo $edit_mode ? $product_to_edit['publisher_name'] : ''; ?>">
            </div>
            <div class="form-group">
                <label for="year">Year</label>
                <input type="number" class="form-control" name="year" value="<?php echo $edit_mode ? $product_to_edit['year'] : ''; ?>">
            </div>
            <div class="form-group">
                <label for="image">Product Image</label>
                <input type="file" class="form-control" name="image" accept="image/*">
                <?php if ($edit_mode && $product_to_edit['image']): ?>
                    <img src="<?php echo htmlspecialchars($product_to_edit['image']); ?>" alt="Product Image" class="img-thumbnail">
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary" name="<?php echo $edit_mode ? 'update_product' : 'add_product'; ?>">
                <?php echo $edit_mode ? 'Update Product' : 'Add Product'; ?>
            </button>
            <?php if ($edit_mode): ?>
                <a href="product.php" class="btn btn-secondary">Cancel</a>
            <?php endif; ?>
        </form>

        <!-- Product List -->
        <h2 class="mt-5">Product List</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Serial No.</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Description</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php $serial_no = 1; // Initialize the serial number ?>
                    <?php while ($product = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $serial_no++; ?></td> <!-- Increment serial number for each row -->
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo htmlspecialchars($product['price']); ?></td>
                            <td><?php echo htmlspecialchars($product['description']); ?></td>
                            <td>
                                <?php if ($product['image']): ?>
                                    <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="Product Image" class="img-thumbnail">
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="?edit_id=<?php echo htmlspecialchars($product['id']); ?>" class="btn btn-warning">Edit</a>
                                <a href="?delete_id=<?php echo htmlspecialchars($product['id']); ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                                <a href="?toggle_status_id=<?php echo htmlspecialchars($product['id']); ?>" class="btn btn-info">
                                    <?php echo $product['status'] === 'active' ? 'Deactivate' : 'Activate'; ?>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No products found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
