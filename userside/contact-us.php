<?php include "conn.php"; ?>

<?php
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
        $stmt->bind_param("ssss", $fullName, $email, $phone, $message);

        if ($stmt->execute()) {
            echo "<p class='alert alert-success'>Thank you, $fullName. Your message has been received!</p>";
        } else {
            echo "<p class='alert alert-danger'>Error: " . $stmt->error . "</p>";
        }

        $stmt->close();
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
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif; /* Custom font */
            background-color: #f8f9fa; /* Light background */
        }
        .container {
            margin-top: 50px;
            border: 1px solid #ccc; /* Border for the form container */
            border-radius: 10px; /* Rounded corners */
            padding: 20px;
            background-color: #ffffff; /* White background for the form */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Soft shadow effect */
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #4CAF50; /* Green border on focus */
        }
        .btn-custom {
            background-color: #4CAF50; /* Custom button color */
            color: white;
            font-weight: bold; /* Bold text */
            padding: 12px 20px; /* Increased padding */
            border-radius: 5px; /* Rounded corners */
            transition: background-color 0.3s, transform 0.3s; /* Transition effects */
            display: flex; /* Flexbox for centering content */
            align-items: center; /* Center vertically */
            justify-content: center; /* Center horizontally */
        }
        .btn-custom:hover {
            background-color: #45a049; /* Darker green on hover */
            transform: scale(1.05); /* Slightly enlarge button on hover */
        }
        .btn-custom:focus {
            outline: none; /* Remove default outline */
            box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.5); /* Green glow on focus */
        }
        .alert {
            margin-top: 20px;
            border-radius: 5px; /* Rounded corners for alerts */
        }
        .form-group label {
            font-weight: 500; /* Bold labels */
        }
        .icon {
            margin-right: 8px; /* Spacing between icon and text */
        }
    </style>
</head>
<body>
    <?php include "navbar.php" ?>
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

            <!-- Submit Button -->
            <button type="submit" class="btn btn-custom btn-block">
                <span class="icon"><i class="fas fa-paper-plane"></i></span>
                Submit
            </button>
        </form>
    </div>
    <?php include "footer.php" ?>
    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Font Awesome for Icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
</body>
</html>
