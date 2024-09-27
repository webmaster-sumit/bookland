<?php
// cart.php
include 'init.php';

// Retrieve cart items
$cart = $_SESSION['cart'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Your Cart</h1>
    <?php if (!empty($cart)): ?>
        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td><?php echo htmlspecialchars($item['price']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Your cart is empty.</p>
    <?php endif; ?>
</body>
</html>
