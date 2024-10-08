<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // User is not logged in, redirect to login page
    header("Location: shop-login.php");
    exit();
}

// User is logged in, you can display their profile information here
$user_id = $_SESSION['user_id'];
$email = $_SESSION['email'];

include "conn.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Form se aayi hui values ko sanitize karte hain
    $fullName = htmlspecialchars(strip_tags(trim($_POST['fullName'])));
    $email = htmlspecialchars(strip_tags(trim($_POST['email'])));
    $phone = htmlspecialchars(strip_tags(trim($_POST['phone'])));
    $message = htmlspecialchars(strip_tags(trim($_POST['message'])));

    // Validations karne ka basic tarika
    $errors = [];

    if (empty($fullName)) {
        $errors[] = "Full Name is required.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid Email is required.";
    }
    if (empty($phone) || !preg_match('/^[0-9]{10}$/', $phone)) {
        $errors[] = "A valid 10-digit Phone Number is required.";
    }
    if (empty($message)) {
        $errors[] = "Message is required.";
    }

    // Agar koi error nahi toh data ko database mein insert karna
    if (empty($errors)) {
        // SQL query for inserting data
        $stmt = $conn->prepare("INSERT INTO contact_form (full_name, email, phone, message) VALUES (?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("ssss", $fullName, $email, $phone, $message);

            if ($stmt->execute()) {
                echo "<p class='alert alert-success'>Thank you, $fullName. Your message has been received!</p>";
            } else {
                echo "<p class='alert alert-danger'>Error: " . $stmt->error . "</p>";
            }

            $stmt->close();
        } else {
            echo "<p class='alert alert-danger'>Error preparing statement: " . $conn->error . "</p>";
        }
    } else {
        // Errors ko display karna
        foreach ($errors as $error) {
            echo "<p class='alert alert-danger'>$error</p>";
        }
    }
}

$conn->close(); // Database connection close
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us Form</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 50px;
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #4CAF50;
        }
        .btn-custom {
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
            padding: 12px 20px;
            border-radius: 5px;
            transition: background-color 0.3s, transform 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .btn-custom:hover {
            background-color: #45a049;
            transform: scale(1.05);
        }
        .btn-custom:focus {
            outline: none;
            box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.5);
        }
        .alert {
            margin-top: 20px;
            border-radius: 5px;
        }
        .form-group label {
            font-weight: 500;
        }
        .icon {
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <?php include "navbar2.php" ?>
    <div class="container">
        <h2 class="text-center">Contact Us</h2>
        <form action="contact-us.php" method="POST" class="mt-4">
            <div class="form-group">
                <label for="fullName">Full Name:</label>
                <input type="text" id="fullName" name="fullName" class="form-control" placeholder="Enter your full name" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number:</label>
                <input type="text" id="phone" name="phone" class="form-control" placeholder="Enter your phone number" pattern="[0-9]{10}" required>
                <small class="form-text text-muted">Please enter a 10-digit phone number.</small>
            </div>
            <div class="form-group">
                <label for="message">Message:</label>
                <textarea id="message" name="message" class="form-control" rows="4" placeholder="Type your message here" required></textarea>
            </div>
            <button type="submit" class="btn btn-lg btn-success btn-block checkout-btn">
    <span class="icon"><i class="fas fa-paper-plane"></i></span>
    Submit
</button>

        </form>
    </div>
    <?php include "footer.php" ?>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
</body>
</html>
