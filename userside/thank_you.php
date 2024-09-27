<?php
session_start();

// Ensure the user has been redirected here properly
if (!isset($_GET['order_id'])) {
    header("Location: checkout.php");
    exit();
}

// Retrieve the order ID
$order_id = htmlspecialchars($_GET['order_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .thank-you-container {
            max-width: 700px;
            margin: 40px auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .thank-you-message {
            margin-top: 20px;
        }
        .thank-you-message h3 {
            font-size: 1.75rem;
            color: #343a40;
        }
        .thank-you-message p {
            font-size: 1.125rem;
            color: #495057;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }
        .alert {
            border-radius: 8px;
        }
        h1 {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #343a40;
            font-weight: 700;
        }
    </style>
    <!-- Redirect to shop.php after 5 seconds (fallback if JavaScript is disabled) -->
    <meta http-equiv="refresh" content="5;url=shop.php">
</head>
<body>

<div class="container">
    <div class="thank-you-container">
        <h1 class="text-center mb-4">Thank You!</h1>

        <!-- Thank You Message -->
        <div class="thank-you-message text-center">
            <h3>Your payment has been successful!</h3>
            <p>Order ID: <strong><?php echo htmlspecialchars($order_id); ?></strong></p>
            <p>Thank you for shopping with us. We appreciate your business.</p>
            <p>You will be redirected to the shop page shortly.</p>
        </div>

        <!-- Optional: Add a button to manually redirect or go back to shop -->
        <div class="text-center mt-4">
            <a href="product.php" class="btn btn-primary">Go to Shop Now</a>
        </div>
    </div>
</div>

<script>
    // Redirect to shop.php after 3 seconds
    setTimeout(function() {
        window.location.href = 'product.php';
    }, 3000);
</script>

</body>
</html>
