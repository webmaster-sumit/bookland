<?php
session_start();
include 'conn.php';

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: adm_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Redirect to welcome.php
    header("Location: welcome.php");
    exit();
}
include 'conn.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS for styling -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Styles to make footer sticky */
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh; /* Make body take at least the full height of the viewport */
        }

        .container {
            flex: 1; /* Allow container to grow and take up available space */
        }

        footer {
            background-color: #f8f9fa; /* Bootstrap light background color, similar to navbar */
            padding: 20px;
            text-align: center;
            /* Optional shadow for better visibility */
            box-shadow: 0 -1px 5px rgba(0,0,0,0.1); 
        }
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>
    
    <div class="container mt-5">
        <h1>Welcome, Admin!</h1>
        <p>This is your admin dashboard where you can manage users, update settings, and monitor system activities.</p>

        <!-- Add a button to redirect to product.php -->
        <form action="product.php" method="POST">
            <button type="submit" class="btn btn-primary mt-3">Go to Product Management</button>
        </form>
    </div>
        <?php include "footer.php"?>
    <!-- JAVASCRIPT FILES ========================================= -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
