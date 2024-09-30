<?php include 'conn.php'?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bookland - Book Store Ecommerce Website</title>
    
    <!-- FAVICONS ICON -->
    <link rel="shortcut icon" type="image/x-icon" href="images/favicon.png" />
    
    <!-- STYLESHEETS -->
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="icons/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    
    <style>
        /* Navbar Styles */
        .navbar {
            background-color: white; /* Dark background */
        }

        .navbar-brand img {
            height: 40px; /* Logo height */
        }

        .navbar-nav .nav-link {
            color: black; /* Link color */
            transition: color 0.3s; /* Transition for hover effect */
        }

        .navbar-nav .nav-link:hover {
            color: #007bff; /* Color on hover */
            text-decoration: underline; /* Underline effect */
        }

        .navbar-toggler {
            border-color: rgba(255, 255, 255, 0.1); /* Toggler border color */
        }

        .navbar-toggler:hover {
            background-color: rgba(0, 123, 255, 0.2); /* Toggler background on hover */
        }

        .navbar-toggler:focus {
            outline: none; /* Remove focus outline */
            box-shadow: none; /* Remove box shadow */
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header class="site-header">
        <!-- Main Header -->
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <!-- Website Logo -->
                <a class="navbar-brand" href="index.php">
                    <img src="../adminside/images/logo.png" alt="Bookland Logo">
                </a>

                <!-- Nav Toggle Button -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Main Nav -->
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
                        <li class="nav-item"><a class="nav-link" href="product.php">Shop</a></li>
                        <li class="nav-item"><a class="nav-link" href="product.php">Cart</a></li>
                        <li class="nav-item"><a class="nav-link" href="checkout.php">Checkout</a></li>
                        <li class="nav-item"><a class="nav-link" href="contact-us.php">Contact Us</a></li>
                        <li class="nav-item"><a class="nav-link" href="carousel.php">Slider</a></li>
                        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
