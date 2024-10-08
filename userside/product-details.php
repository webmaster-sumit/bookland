<?php
session_start();
include 'conn.php';  // Adjust the path to conn.php if necessary

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

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
            <a href="shoping.php?view=cart&id=<?php echo $product['id']; ?>" class="btn btn-primary add-to-cart-btn">Add to Cart</a>
        </div>

        <!-- Other Products -->
        <div class="col-md-4 other-products">
            <h2>Other Products</h2>
            <?php while ($otherProduct = $otherProductsResult->fetch_assoc()): ?>
                <div class="product">
                    <a href="product-details.php?id=<?php echo $otherProduct['id']; ?>">
                        <?php
                        $otherImageSrc = '../adminside/' . $otherProduct['image'];
                        if (@file_exists($otherImageSrc) && !empty($otherProduct['image'])): ?>
                            <img src="<?php echo htmlspecialchars($otherImageSrc, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($otherProduct['name']); ?>">
                        <?php else: ?>
                            <img src="path/to/placeholder-image.jpg" alt="No Image Available">
                        <?php endif; ?>
                        <p><?php echo htmlspecialchars($otherProduct['name']); ?></p>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

</body>
</html>
