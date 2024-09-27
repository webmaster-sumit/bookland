<?php
session_start();
include 'conn.php';  // Ensure this path is correct

// Ensure the user has an order ID in session
if (!isset($_SESSION['order_id'])) {
    header("Location: checkout.php");
    exit();
}

// Function to retrieve the order details
function get_order_details($conn, $order_id) {
    $query = "SELECT * FROM orders WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Get the order details
$order_id = $_SESSION['order_id'];
$order_details = get_order_details($conn, $order_id);

if (!$order_details) {
    echo "Order not found.";
    exit();
}

// Simulate payment processing (in real applications, integrate with payment gateway API)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Payment processing logic goes here

    // Assuming payment is successful, clear the order ID and redirect to thank you page
    unset($_SESSION['order_id']);
    header("Location: thank_you.php?order_id=" . urlencode($order_id));
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f0f2f5;
        }
        .payment-container {
            max-width: 600px;
            margin: 40px auto;
            padding: 30px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .payment-form {
            margin-top: 20px;
        }
        .form-group label {
            font-weight: 500;
            color: #495057;
        }
        .btn-block {
            padding: 15px;
            font-size: 16px;
        }
        .alert {
            margin-bottom: 20px;
        }
        h1, h3 {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="payment-container">
        <h1 class="text-center mb-4">Payment</h1>

        <!-- Order Details -->
        <div class="order-details">
            <h3>Order Details</h3>
            <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order_id); ?></p>
            <p><strong>Total Amount:</strong> ₹<?php echo number_format($order_details['total'], 2); ?></p>
        </div>

        <!-- Payment Form -->
        <form action="payment.php" method="POST" class="payment-form">
            <div class="form-group">
                <label for="card_number">Card Number</label>
                <input type="text" name="card_number" id="card_number" class="form-control" required placeholder="Enter your card number">
            </div>
            <div class="form-group">
                <label for="card_expiry">Expiry Date (MM/YY)</label>
                <input type="text" name="card_expiry" id="card_expiry" class="form-control" required placeholder="MM/YY">
            </div>
            <div class="form-group">
                <label for="card_cvc">CVC</label>
                <input type="text" name="card_cvc" id="card_cvc" class="form-control" required placeholder="CVC">
            </div>
            <button type="submit" class="btn btn-success btn-block">Pay Now</button>
        </form>
    </div>
</div>

</body>
</html>
