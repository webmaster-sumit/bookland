<?php
include 'conn.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <style>
        .navbar {
            background-color: #333;
            padding: 10px 20px;
            text-align: center;
            position: sticky; /* Keeps the navbar fixed at the top */
            top: 0;
            width: 100%;
            z-index: 1000; /* Ensures the navbar stays above other content */
        }

        .navbar a {
            color: #fff;
            text-decoration: none;
            margin: 0 15px;
            font-size: 18px;
            padding: 10px 15px;
            border-radius: 4px;
            transition: background-color 0.3s, color 0.3s; /* Smooth transition effect */
        }

        .navbar a:hover {
            background-color: #555;
            color: #fff; /* Ensures text color stays white on hover */
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="adm_login.php">login</a>
        <a href="adm_register.php">register</a>
    </div>
</body>
</html>
