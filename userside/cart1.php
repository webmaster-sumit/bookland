<?php
session_start(); // Start the session
include 'conn.php'; // Database connection

// Check if cart exists, if not, initialize it
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Update cart quantities
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Increment quantity
    if (isset($_POST['increment'])) {
        $key = $_POST['increment'];
        if (isset($_SESSION['cart'][$key])) {
            $_SESSION['cart'][$key]['quantity']++; // Increase the quantity by 1
        }
        header('Location: cart1.php'); // Redirect to refresh the page
        exit;
    }

    // Decrement quantity
    if (isset($_POST['decrement'])) {
        $key = $_POST['decrement'];
        if (isset($_SESSION['cart'][$key]) && $_SESSION['cart'][$key]['quantity'] > 1) {
            $_SESSION['cart'][$key]['quantity']--; // Decrease the quantity by 1
        } else {
            unset($_SESSION['cart'][$key]); // Remove item if quantity is 0
            $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindex array
        }
        header('Location: cart1.php'); // Redirect to refresh the page
        exit;
    }

    // Update cart quantities from input
    if (isset($_POST['update_cart'])) {
        $key = $_POST['update_cart'];
        $new_quantity = intval($_POST['quantity'][$key]);
        if ($new_quantity > 0) {
            $_SESSION['cart'][$key]['quantity'] = $new_quantity;
        } else {
            unset($_SESSION['cart'][$key]); // Remove item if quantity is 0 or invalid
            $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindex array
        }
        header('Location: cart1.php'); // Redirect to refresh the page
        exit;
    }

    // Remove item from cart
    if (isset($_POST['remove_item'])) {
        $key = $_POST['remove_item'];
        unset($_SESSION['cart'][$key]);
        $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindex array
        header('Location: cart1.php'); // Redirect to refresh the page
        exit;
    }

    // Handle checkout process
    if (isset($_POST['checkout'])) {
        header('Location: checkout.php'); // Redirect to checkout page
        exit;
    }
}

// Function to calculate the total price
function calculate_total() {
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return $total;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .cart-table th, .cart-table td { text-align: center; }
        .cart-table img { width: 150px; height: 150px; object-fit: cover; }
        .checkout-btn { background-color: #28a745; color: white; margin-top: 20px; width: 100%; }
        .checkout-btn:hover { background-color: #218838; }
        .btn-increment, .btn-decrement { width: 40px; }
    </style>
</head>
<body>
<?php include 'navbar2.php'; ?>

<div class="container my-4">
    <h1 class="text-center mb-4">Your Cart</h1>
    <?php if (!empty($_SESSION['cart'])): ?>
        <form action="cart1.php" method="POST">
            <table class="table table-bordered cart-table">
                <thead class="thead-light">
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
                                <div class="d-flex justify-content-center">
                                    <button type="submit" name="decrement" value="<?php echo $key; ?>" class="btn btn-secondary btn-decrement">-</button>
                                    <input type="number" name="quantity[<?php echo $key; ?>]" value="<?php echo $item['quantity']; ?>" min="1" class="form-control" style="width: 60px; text-align: center; margin: 0 10px;">
                                    <button type="submit" name="increment" value="<?php echo $key; ?>" class="btn btn-secondary btn-increment">+</button>
                                </div>
                            </td>
                            <td>₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                            <td>
                                <button type="submit" name="update_cart" value="<?php echo $key; ?>" class="btn btn-warning btn-sm">Update</button>
                                <button type="submit" name="remove_item" value="<?php echo $key; ?>" class="btn btn-danger btn-sm">Remove</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <h4 class="text-right">Total: ₹<?php echo number_format(calculate_total(), 2); ?></h4>
            <button type="submit" name="checkout" class="btn btn-lg btn-success btn-block checkout-btn">
                <i class="fas fa-shopping-cart"></i> Proceed to Checkout
            </button>
        </form>
    <?php else: ?>
        <p class="text-center">Your cart is empty. <a href="product.php">Continue shopping</a>.</p>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
