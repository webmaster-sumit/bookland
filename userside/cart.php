<?php
session_start(); // Start the session
include 'conn.php';

// Check if cart exists, if not, initialize it
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Update cart quantities
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Update quantities
    if (isset($_POST['update_cart'])) {
        $key = intval($_POST['update_cart']);
        $new_quantity = intval($_POST['quantity'][$key]);
        if ($new_quantity > 0) {
            $_SESSION['cart'][$key]['quantity'] = $new_quantity;
        } else {
            // If quantity is 0 or negative, remove item
            unset($_SESSION['cart'][$key]);
        }
        header('Location: cart.php');
        exit;
    }

    // Remove item from cart
    if (isset($_POST['remove_item'])) {
        $key = intval($_POST['remove_item']);
        unset($_SESSION['cart'][$key]);
        $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindex array
        header('Location: cart.php');
        exit;
    }

    // Handle checkout process
    if (isset($_POST['checkout'])) {
        header('Location: checkout.php');
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        .cart-table th, .cart-table td { text-align: center; }
        .cart-table img { width: 150px; height: 150px; object-fit: cover; }
        .checkout-btn { background-color: #28a745; color: white; margin-top: 20px; width: 100%; }
        .checkout-btn:hover { background-color: #218838; }
        .quantity-btn { width: 30px; }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container my-4">
    <h1 class="text-center mb-4">Your Cart</h1>
    <?php if (!empty($_SESSION['cart'])): ?>
        <form action="cart.php" method="POST">
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
                                <div class="d-flex justify-content-center align-items-center">
                                    <button type="button" class="btn btn-outline-secondary quantity-btn" onclick="updateQuantity(<?php echo $key; ?>, -1)">-</button>
                                    <input type="number" id="quantity_<?php echo $key; ?>" name="quantity[<?php echo $key; ?>]" value="<?php echo $item['quantity']; ?>" min="1" class="form-control mx-2" style="width: 80px;">
                                    <button type="button" class="btn btn-outline-secondary quantity-btn" onclick="updateQuantity(<?php echo $key; ?>, 1)">+</button>
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

<script>
    function updateQuantity(key, change) {
        const quantityInput = document.getElementById('quantity_' + key);
        let quantity = parseInt(quantityInput.value);
        quantity = Math.max(1, quantity + change); // Prevent going below 1
        quantityInput.value = quantity;
    }
</script>
</body>
</html>
