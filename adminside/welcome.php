<?php
session_start();
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Redirect to product.php
    header("Location: product.php");
    exit();
}
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
</head>
<body>
<?php include 'nav.php'; ?>
    <div class="container mt-5">
        <h1>Welcome, Admin!</h1>
        <p>This is your admin dashboard where you can manage users, update settings, and monitor system activities.</p>

        <!-- Add a button to redirect to product.php -->
        <form action="welcome.php" method="POST">
            <button type="submit" class="btn btn-primary mt-3">Go to Product Management</button>
        </form>
    </div>

    <?php include 'footer.php'; ?>    
    <!-- JAVASCRIPT FILES ========================================= -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
