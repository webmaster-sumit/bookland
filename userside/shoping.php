<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // User is not logged in, redirect to login page
    header("Location: shop-login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$email = $_SESSION['email'];
include 'conn.php';

// Initialize the cart if it doesn't exist yet
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle adding products to the cart
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['product_id'])) {
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

        if (!$product_exists) {
            $_SESSION['cart'][] = [
                'id' => $product_id,
                'name' => $product_name,
                'price' => $product_price,
                'quantity' => 1,
                'image' => $product_image
            ];
        }

        header('Location: shoping.php?view=cart');
        exit;
    }

    // Update cart quantities
    if (isset($_POST['update_cart'])) {
        $key = intval($_POST['update_cart']);
        $new_quantity = intval($_POST['quantity'][$key]);
        if ($new_quantity > 0) {
            $_SESSION['cart'][$key]['quantity'] = $new_quantity;
        }
        header('Location: shoping.php?view=cart');
        exit;
    }

    // Remove item from the cart
    if (isset($_POST['remove_item'])) {
        $key = intval($_POST['remove_item']);
        unset($_SESSION['cart'][$key]);
        $_SESSION['cart'] = array_values($_SESSION['cart']); // Re-index the cart array
        header('Location: shoping.php?view=cart');
        exit;
    }

    // Handle checkout process
    if (isset($_POST['checkout'])) {
        header('Location: checkout.php');
        exit;
    }
}

// Function to calculate the total price of the cart
function calculate_total() {
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return $total;
}

// Fetch all products from the database
$query = "SELECT * FROM products ORDER BY id DESC";
$result = $conn->query($query);
if (!$result) {
    die("Database query failed: " . $conn->error);
}

// Determine the view type from the URL parameter
$view = isset($_GET['view']) ? $_GET['view'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="shortcut icon" type="image/x-icon" href="images/favicon.png" />
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="icons/fontawesome/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/bootstrap-select/dist/css/bootstrap-select.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/swiper/swiper-bundle.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700;800&family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        .product-card { 
            border: 1px solid #ddd; 
            border-radius: 8px; 
            padding: 15px; 
            margin-bottom: 20px; 
        }
        .product-card img { 
            width: 100%; 
            height: 200px; 
            object-fit: cover; 
        }
        .cart-table th, .cart-table td { 
            text-align: center; 
        }
        .cart-table img {
            width: 150px; 
            height: 150px; 
            object-fit: cover;
        }
        .checkout-btn { 
            background-color: #28a745; 
            color: white; 
            margin-top: 20px; 
            width: 100%; 
        }
        .checkout-btn:hover { 
            background-color: #218838; 
        }
    </style>
</head>
<body>
<?php include 'navbar2.php'; ?>
<div class="page-wraper">
    <div id="loading-area" class="preloader-wrapper-1">
        <div class="preloader-inner">
            <div class="preloader-shade"></div>
            <div class="preloader-wrap"></div>
            <div class="preloader-wrap wrap2"></div>
            <div class="preloader-wrap wrap3"></div>
            <div class="preloader-wrap wrap4"></div>
            <div class="preloader-wrap wrap5"></div>
        </div> 
    </div>
    <div class="container">
        <!-- Display Products if not in 'cart' view -->
        <?php if ($view !== 'cart'): ?>
            <h1 class="text-center mb-4">Books Catalog</h1>
            <div class="row">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col-md-4 col-sm-6 mb-4">
                        <div class="product-card">
                            <a href="product-details.php?id=<?php echo $row['id']; ?>">
                                <img src="../adminside/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" class="img-fluid">
                            </a>
                            <h4><?php echo htmlspecialchars($row['name']); ?></h4>
                            <p>Price: ₹<?php echo number_format($row['price'], 2); ?></p>
                            <p><?php echo htmlspecialchars(substr($row['description'], 0, 100)) . '...'; ?></p>
                            <form action="shoping.php" method="POST">
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
        <?php endif; ?>

        <!-- Display Cart if in 'cart' view -->
        <?php if ($view === 'cart'): ?>
            <h1 class="text-center mb-4">Your Cart</h1>
            <?php if (!empty($_SESSION['cart'])): ?>
                <form action="shoping.php" method="POST">
                    <table class="table table-bordered cart-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Image</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($_SESSION['cart'] as $key => $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                                    <td>
                                        <a href="product-details.php?id=<?php echo $item['id']; ?>" class="product-image-link">
                                            <?php if (file_exists("../adminside/" . $item['image'])): ?>
                                                <img src="../adminside/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="img-fluid">
                                            <?php else: ?>
                                                <p>Image not available</p>
                                            <?php endif; ?>
                                        </a>
                                    </td>
                                    <td>₹<?php echo number_format($item['price'], 2); ?></td>
                                    <td>
                                        <input type="number" name="quantity[<?php echo $key; ?>]" 
                                               value="<?php echo isset($_POST['quantity'][$key]) ? intval($_POST['quantity'][$key]) : $item['quantity']; ?>" 
                                               min="1" class="form-control" style="width: 80px; display: inline-block;">
                                    </td>
                                    <td>₹<?php echo number_format($item['price'] * (isset($_POST['quantity'][$key]) ? intval($_POST['quantity'][$key]) : $item['quantity']), 2); ?></td>
                                    <td>
                                        <button type="submit" name="update_cart" value="<?php echo $key; ?>" class="btn btn-warning btn-sm">Update Cart</button>
                                        <button type="submit" name="remove_item" value="<?php echo $key; ?>" class="btn btn-danger btn-sm">Remove</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <h3 class="text-right">Total: ₹<?php echo number_format(calculate_total(), 2); ?></h3>
                    <button type="submit" name="checkout" class="btn btn-success checkout-btn">
                     <i class="fas fa-shopping-cart"></i> Proceed to Checkout
                    </button>
                </form>
            <?php else: ?>
                <p class="text-center">Your cart is empty!</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
<?php include "footer.php"?>
<!-- Include Bootstrap JS and other scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="vendor/bootstrap-select/dist/js/bootstrap-select.min.js"></script>
<script src="vendor/swiper/swiper-bundle.min.js"></script>
<script src="js/custom.js"></script>
</body>
</html>
