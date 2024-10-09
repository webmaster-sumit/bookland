<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: shop-login.php");
    exit();
}

include 'conn.php';

// Initialize the cart if it doesn't exist yet
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle adding products to the cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);
    $product_name = htmlspecialchars($_POST['product_name']);
    $product_price = floatval($_POST['product_price']);
    $product_image = htmlspecialchars($_POST['product_image']);

    // Check if the product already exists in the cart
    $product_exists = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $product_id) {
            $item['quantity']++;
            $product_exists = true;
            break;
        }                                                                                                                                                                                                                                                                                                         

    }

    // If the product does not exist, add it to the cart
    if (!$product_exists) {
        $_SESSION['cart'][] = [
            'id' => $product_id,
            'name' => $product_name,
            'price' => $product_price,
            'quantity' => 1,
            'image' => $product_image
        ];
    }

    // Redirect to cart.php after adding to cart
    header('Location: cart.php'); // Redirect to the cart page
    exit;
}

// Fetch all products from the database
$query = "SELECT * FROM products ORDER BY id DESC";
$result = $conn->query($query);
if (!$result) {
    die("Database query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="icons/fontawesome/css/all.min.css">
    <style>
        .product-card { 
            border: 1px solid #ddd; 
            border-radius: 8px; 
            padding: 15px; 
            margin-bottom: 20px; 
            transition: box-shadow 0.3s;
        }
        .product-card:hover { 
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); 
        }
        .product-card img { 
            width: 100%; 
            height: 200px; 
            object-fit: cover; 
        }
        .add-to-cart-btn { 
            background-color: #007bff; 
            color: white; 
            margin-top: 10px; 
        }
        .add-to-cart-btn:hover { 
            background-color: #0056b3; 
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">
    <h1 class="text-center mb-4">Books Catalog</h1>
    <div class="row">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="col-md-4 col-sm-6 mb-4">
                <div class="product-card">
                    <a href="shop-details.php?id=<?php echo $row['id']; ?>">
                        <img src="../adminside/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" class="img-fluid">
                    </a>
                    <h4><?php echo htmlspecialchars($row['name']); ?></h4>
                    <p>Price: ₹<?php echo number_format($row['price'], 2); ?></p>
                    <p><?php echo htmlspecialchars(substr($row['description'], 0, 100)) . '...'; ?></p>
                    <form action="shopping.php" method="POST">
                        <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($row['name']); ?>">
                        <input type="hidden" name="product_price" value="<?php echo $row['price']; ?>">
                        <input type="hidden" name="product_image" value="<?php echo htmlspecialchars($row['image']); ?>">
                        <button type="submit" class="btn add-to-cart-btn btn-block">ADD TO CART</button>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
