<?php
session_start();
include 'conn.php';  // Adjust the path to conn.php if necessary

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to calculate total price
function calculate_total() {
    $total = 0;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['price'] * $item['quantity'];
        }
    }
    return $total;
}

// Handle form submission for checkout
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve customer details from POST and sanitize input
    $customer_name = htmlspecialchars(trim($_POST['customer_name']));
    $customer_email = htmlspecialchars(trim($_POST['customer_email']));
    $customer_address = htmlspecialchars(trim($_POST['customer_address']));
    $customer_phone = htmlspecialchars(trim($_POST['customer_phone']));

    // Validate customer details
    if (empty($customer_name) || empty($customer_email) || empty($customer_address) || empty($customer_phone)) {
        $error_message = "All fields are required.";
    } elseif (!filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } elseif (!preg_match('/^[0-9]{10}$/', $customer_phone)) {
        $error_message = "Invalid phone number. Please enter a 10-digit number.";
    } else {
        // Insert order details into the database
        $order_total = calculate_total();
        $query = "INSERT INTO orders (name, email, address, phone, total) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ssssd', $customer_name, $customer_email, $customer_address, $customer_phone, $order_total);

        if ($stmt->execute()) {
            $order_id = $stmt->insert_id; // Get the inserted order ID

            // Insert order items into the database
            $query = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($query);

            foreach ($_SESSION['cart'] as $item) {
                $stmt->bind_param('iiid', $order_id, $item['id'], $item['quantity'], $item['price']);
                $stmt->execute();
            }

            // Store order ID in session to be used in payment page
            $_SESSION['order_id'] = $order_id;

            // Clear the cart after successful order
            $_SESSION['cart'] = [];

            // Redirect to payment page
            header("Location: payment.php");
            exit();
        } else {
            $error_message = "An error occurred while processing your order.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f0f2f5;
        }
        .checkout-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 30px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .checkout-summary { 
            border: 1px solid #ddd; 
            border-radius: 8px; 
            padding: 15px; 
            margin-bottom: 20px; 
        }
        .checkout-summary h3 {
            margin-bottom: 20px;
            font-weight: bold;
            color: #343a40;
        }
        .checkout-form {
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
        .loading-indicator {
            display: none;
            text-align: center;
            margin-top: 20px;
        }
        /* Styling the table */
        table.table-bordered {
            border: 1px solid #dee2e6;
        }
        table.table-bordered th, 
        table.table-bordered td {
            text-align: center;
            vertical-align: middle;
        }
        table.table-bordered th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        table.table-bordered td {
            color: #6c757d;
        }
        h1, h3 {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        h1 {
            font-size: 2.5rem;
        }
    </style>
</head>
<body>
<?php include 'navbar2.php' ?>
<div class="container">
    <div class="checkout-container">
        <h1 class="text-center mb-4">Checkout</h1>

        <!-- Display error or success messages -->
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
        <?php elseif (!empty($success_message)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>

        <!-- Order Summary Section -->
        <div class="checkout-summary">
            <h3>Order Summary</h3>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($_SESSION['cart'])): ?>
                        <?php foreach ($_SESSION['cart'] as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td>₹<?php echo number_format($item['price'], 2); ?></td>
                                <td>₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center">Your cart is empty.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <h3 class="text-right">Total: ₹<?php echo number_format(calculate_total(), 2); ?></h3>
        </div>

        <!-- Checkout Form -->
        <form action="checkout.php" method="POST" class="checkout-form">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="customer_name">Name</label>
                    <input type="text" name="customer_name" id="customer_name" class="form-control" required placeholder="Your Name">
                </div>
                <div class="form-group col-md-6">
                    <label for="customer_email">Email</label>
                    <input type="email" name="customer_email" id="customer_email" class="form-control" required placeholder="Your Email">
                </div>
            </div>
            <div class="form-group">
                <label for="customer_address">Address</label>
                <textarea name="customer_address" id="customer_address" class="form-control" rows="3" required placeholder="Your Address"></textarea>
            </div>
            <div class="form-group">
                <label for="customer_phone">Phone</label>
                <input type="text" name="customer_phone" id="customer_phone" class="form-control" required pattern="[0-9]{10}" title="Please enter a valid 10-digit phone number" placeholder="Your Phone Number">
            </div>
            <button type="submit" class="btn btn-success btn-block">Place Order</button>
        </form>

        <div class="loading-indicator">
            <img src="loading.gif" alt="Loading..." />
            <p>Processing your order, please wait...</p>
        </div>
    </div>
</div>

<script>
    // JavaScript to show loading indicator when the form is submitted
    document.querySelector('.checkout-form').addEventListener('submit', function() {
        document.querySelector('.loading-indicator').style.display = 'block';
    });
</script>
    <?php include 'footer.php'?>
</body>
</html>
