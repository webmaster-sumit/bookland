<?php
session_start();
include 'conn.php';  // Adjust the path to conn.php if necessary

// Fetch product details based on ID from query string
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$query = "SELECT * FROM products WHERE id = $product_id";
$result = $conn->query($query);

if (!$result || $result->num_rows == 0) {
    die("Product not found.");
}

$product = $result->fetch_assoc();

// Fetch other products
$otherProductsQuery = "SELECT * FROM products WHERE id != $product_id ORDER BY id DESC";
$otherProductsResult = $conn->query($otherProductsQuery);

if (!$otherProductsResult) {
    die("Database query failed: " . $conn->error);
}

// Handle Add to Cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = (int)$_POST['product_id'];
    $product_name = htmlspecialchars($_POST['product_name']);
    $product_price = (float)$_POST['product_price'];
    $product_image = htmlspecialchars($_POST['product_image']);

    // Initialize the cart if it doesn't exist
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Check if the product already exists in the cart
    $product_exists = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $product_id) {
            $item['quantity']++;
            $product_exists = true;
            break;
        }
    }

    // If it doesn't exist, add it
    if (!$product_exists) {
        $_SESSION['cart'][] = [
            'id' => $product_id,
            'name' => $product_name,
            'price' => $product_price,
            'quantity' => 1,
            'image' => $product_image
        ];
    }

    // Redirect to the cart page with the product ID
    header("Location: shoping.php?view=cart&id=$product_id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .container {
            margin-top: 20px;
        }
        .product-detail {
            margin-bottom: 20px;
        }
        .product-detail img {
            max-width: 100%;
            height: auto;
        }
        .other-products {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
        }
        .other-products img {
            width: 100px;
            height: 100px;
            object-fit: cover;
        }
        .other-products .product {
            margin-bottom: 10px;
        }
        .add-to-cart-btn {
            width: 100%;
            text-align: center;
            margin-top: 20px;
        }
        @media (max-width: 768px) {
            .product-detail img {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row">
        <!-- Product Details -->
        <div class="col-md-8 product-detail">
            <h1><?php echo htmlspecialchars($product['name']); ?></h1>
            <?php
            $imageSrc = '../adminside/' . $product['image'];
            if (@file_exists($imageSrc) && !empty($product['image'])): ?>
                <img src="<?php echo htmlspecialchars($imageSrc, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
            <?php else: ?>
                <img src="path/to/placeholder-image.jpg" alt="No Image Available">
            <?php endif; ?>
            <p><strong>Price:</strong> ₹<?php echo number_format($product['price'], 2); ?></p>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($product['description']); ?></p>
            <p><strong>Author:</strong> <?php echo htmlspecialchars($product['author'] ?? 'Unknown Author'); ?></p>
            <p><strong>Publisher:</strong> <?php echo htmlspecialchars($product['publisher'] ?? 'Unknown Publisher'); ?></p>
            <p><strong>Year:</strong> <?php echo htmlspecialchars($product['year'] ?? 'Unknown Year'); ?></p>

            <!-- Add to Cart Form -->
            <form action="shop-details.php?id=<?php echo $product['id']; ?>" method="POST">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['name']); ?>">
                <input type="hidden" name="product_price" value="<?php echo $product['price']; ?>">
                <input type="hidden" name="product_image" value="<?php echo htmlspecialchars($product['image']); ?>">
                <button type="submit" name="add_to_cart" class="btn btn-primary add-to-cart-btn">Add to Cart</button>
            </form>
        </div>

        <!-- Other Products -->
        <div class="col-md-4 other-products">
            <h2>Other Products</h2>
            <?php while ($otherProduct = $otherProductsResult->fetch_assoc()): ?>
                <div class="product">
                    <a href="shop-details.php?id=<?php echo $otherProduct['id']; ?>">
                        <?php
                        $otherImageSrc = '../adminside/' . $otherProduct['image'];
                        if (@file_exists($otherImageSrc) && !empty($otherProduct['image'])): ?>
                            <img src="<?php echo htmlspecialchars($otherImageSrc, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($otherProduct['name']); ?>">
                        <?php else: ?>
                            <img src="path/to/placeholder-image.jpg" alt="No Image Available">
                        <?php endif; ?>
                        <p><?php echo htmlspecialchars($otherProduct['name']); ?></p>
                        <p><strong>Price:</strong> ₹<?php echo number_format($otherProduct['price'], 2); ?></p> <!-- Display Price -->
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

</body>
</html>
