<?php
session_start(); // Start the session
include 'conn.php';

// Initialize the cart if it doesn't exist yet (optional, can remove this line if not needed)
if (!isset($_SESSION['cart.php'])) 
    $_SESSION['cart'] = [];

// Handle adding products to the cart
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if a product is being added to the cart
    if (isset($_POST['product_id'])) {
        $product_id = $_POST['product_id'];
        $product_name = $_POST['product_name'];
        $product_price = $_POST['product_price'];
        $product_image = $_POST['product_image'];   

        $product_exists = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] == $product_id) {
                $item['quantity']++;
                $product_exists = true;
                break;
            }
        }

        // If the product doesn't exist in the cart, add it
        if (!$product_exists) {
            $_SESSION['cart'][] = [
                'id' => $product_id,
                'name' => $product_name,
                'price' => $product_price,
                'quantity' => 1,
                'image' => $product_image
            ];
        }

        // Redirect to the cart page
        header('Location: cart.php');
        exit;
    }
}

// Fetch all active products from the database
$query = "SELECT * FROM products WHERE status = 'active' ORDER BY id DESC";
$result = $conn->query($query);

// Check for database query error
if (!$result) {
    die("Database query failed: " . $conn->error);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <meta name="description" content="Bookland-Book Store Ecommerce Website" />
    <meta property="og:title" content="Bookland-Book Store Ecommerce Website" />
    <meta property="og:description" content="Bookland-Book Store Ecommerce Website" />
    <meta property="og:image" content="../../makaanlelo.com/tf_products_007/bookland/xhtml/social-image.html" />
    <meta name="format-detection" content="telephone=no">

    <!-- FAVICONS ICON -->
    <link rel="shortcut icon" type="image/x-icon" href="images/favicon.png" />

    <!-- STYLESHEETS -->
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="icons/fontawesome/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/bootstrap-select/dist/css/bootstrap-select.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/swiper/swiper-bundle.min.css">

    <style>
        .product-card { 
            border: 1px solid #ddd; 
            border-radius: 8px; 
            padding: 15px; 
            margin-bottom: 20px; 
            transition: transform 0.2s; 
        }
        .product-card:hover { 
            transform: scale(1.05); 
        }
        .product-card img { 
            width: 100%; 
            height: 200px; 
            object-fit: cover; 
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="page-wraper">
    <div class="container my-4">
        <!-- Display Products -->
        <h1 class="text-center mb-4">Books Catalog</h1>
        <div class="row">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-4 col-sm-6 mb-4">
                    <div class="product-card shadow">
                        <a href="product-details.php?id=<?php echo $row['id']; ?>">
                            <img src="../adminside/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" class="img-fluid">
                        </a>
                        <h4 class="mt-2"><?php echo htmlspecialchars($row['name']); ?></h4>
                        <p class="text-success">Price: ₹<?php echo number_format($row['price'], 2); ?></p>
                        <p><?php echo htmlspecialchars(substr($row['description'], 0, 100)) . '...'; ?></p>
                        <form action="product.php" method="POST">
                            <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($row['name']); ?>">
                            <input type="hidden" name="product_price" value="<?php echo $row['price']; ?>">
                            <input type="hidden" name="product_image" value="<?php echo htmlspecialchars($row['image']); ?>">
                            <button type="submit" class="btn btn-primary btn-block">ADD TO CART</button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
