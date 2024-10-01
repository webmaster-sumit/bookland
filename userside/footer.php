<?php
include 'conn.php';

// Fetching only active services from the database
$result = $conn->query("SELECT * FROM admin_services WHERE status = 'active'");
$services = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Services</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f0f0f0;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .service-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
        }
        .service-item {
            width: calc(33.333% - 20px);
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #fff;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        .service-item:hover {
            transform: translateY(-5px);
        }
        .service-image {
            width: 100%;
            height: 200px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .service-image img {
            width: 90%;
            height: 90%;
            object-fit: cover;
        }
        .service-content {
            padding: 15px;
        }
        .service-content h2 {
            font-size: 1.5em;
            margin-bottom: 10px;
            color: #007BFF;
        }
        .service-content p {
            font-size: 1em;
            line-height: 1.5;
            color: #666;
        }
        .add-to-cart-button {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 15px;
            background-color: #28a745;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .add-to-cart-button:hover {
            background-color: #218838;
        }
        /* Footer Styles */
        .site-footer { background-color: #343a40; color: white; padding: 30px 0; }
        .footer-title { font-size: 1.25rem; margin-bottom: 15px; }
        .dz-social-icon ul { display: flex; padding: 0; list-style: none; }
        .dz-social-icon ul li { margin-right: 10px; }
        .dz-social-icon a { color: #fff; font-size: 18px; transition: color 0.3s; }
        .dz-social-icon a:hover { color: #007bff; }
        .footer-bottom { border-top: 1px solid #484848; padding: 10px 0; }
        .scroltop { position: fixed; bottom: 20px; right: 20px; background-color: #007bff; color: white; border: none; border-radius: 5px; padding: 10px 15px; cursor: pointer; display: none; }
        .scroltop:hover { background-color: #0056b3; }
    </style>
</head>
<body>
    


    <!-- Footer Start -->
    <footer class="site-footer mt-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    <div class="footer-logo">
                        <a href="index.php">
                            <img src="<?php echo htmlspecialchars('../adminside/images/logo-white.png'); ?>" alt="Bookland Logo" style="max-width: 100%;">
                        </a>
                    </div>
                    <p>Bookland is a Book Store Ecommerce Website Template by DexignZone. Your one-stop solution for books.</p>
                    <div class="dz-social-icon">
                        <ul>
                            <li><a href="https://www.facebook.com/dexignzone" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a></li>
                            <li><a href="https://www.youtube.com/channel/UCGL8V6uxNNMRrk3oZfVct1g" target="_blank" aria-label="YouTube"><i class="fab fa-youtube"></i></a></li>
                            <li><a href="https://www.linkedin.com/showcase/3686700/admin/" target="_blank" aria-label="LinkedIn"><i class="fab fa-linkedin"></i></a></li>
                            <li><a href="https://www.instagram.com/website_templates__/" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3">
                    <h5 class="footer-title">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="about-us.php">About Us</a></li>
                        <li><a href="contact-us.php">Contact Us</a></li>
                        <li><a href="privacy-policy.php">Privacy Policy</a></li>
                        <li><a href="pricing.php">Pricing</a></li>
                        <li><a href="faq.php">FAQ</a></li>
                    </ul>
                </div>
                <div class="col-lg-3">
                    <h5 class="footer-title">Resources</h5>
                    <ul class="list-unstyled">
                        <li><a href="services.php">Download</a></li>
                        <li><a href="help-desk.php">Help Center</a></li>
                        <li><a href="shop-cart.php">Cart</a></li>
                        <li><a href="shop-login.php">Login</a></li>
                        <li><a href="about-us.php">Partner</a></li>
                    </ul>
                </div>
                <div class="col-lg-3">
                    <h5 class="footer-title">Get in Touch</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-map-marker-alt"></i> 832 Thompson Drive, San Francisco, CA 94107, US</li>
                        <li><i class="fas fa-phone"></i> +123 345 123 556</li>
                        <li><i class="fas fa-envelope"></i> support@bookland.id</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom text-center">
                <p class="copyright-text">© 2022 Bookland. All Rights Reserved. Made with <span>&hearts;</span> by <a href="https://dexignzone.com/" class="text-white">DexignZone</a></p>
            </div>
        </div>
    </footer>
    <!-- Footer End -->

    <!-- Scroll to Top Button -->
    <button class="scroltop" type="button" aria-label="Scroll to Top" title="Scroll to Top"><i class="fas fa-arrow-up"></i></button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.onscroll = function() {
            var scrollButton = document.querySelector('.scroltop');
            scrollButton.style.display = (document.body.scrollTop > 50 || document.documentElement.scrollTop > 50) ? "block" : "none";
        };
        document.querySelector('.scroltop').onclick = function() {
            window.scrollTo({top: 0, behavior: 'smooth'});
        };
    </script>
</body>
</html>
