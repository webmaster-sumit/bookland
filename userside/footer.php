<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>One Page Footer Example</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            padding-bottom: 20px; /* Adjust this if you want space below content */
        }

        footer {
            background-color: #333;
            color: #fff;
            padding: 10px 0; /* Reduced padding */
            text-align: center;
            width: 100%;      /* Make it full width */
            z-index: 1000;    /* Ensure it is above other elements */
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .footer-content {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .footer-section {
            flex: 1;
            padding: 5px; /* Reduced padding */
            min-width: 150px; /* Adjusted minimum width */
        }

        .footer-section h3 {
            font-size: 1em; /* Reduced font size */
            margin-bottom: 5px; /* Reduced margin */
        }

        .footer-section p,
        .footer-section ul {
            font-size: 0.8em; /* Reduced font size */
            margin-bottom: 5px; /* Reduced margin */
        }

        .footer-section ul {
            list-style: none;
            padding: 0;
        }

        .footer-section ul li {
            margin-bottom: 3px; /* Reduced margin */
        }

        .footer-section ul li a {
            color: #fff;
            text-decoration: none;
        }

        .footer-section ul li a:hover {
            text-decoration: underline;
        }

        .footer-bottom {
            margin-top: 20px; /* Space above bottom text */
            font-size: 0.8em;
        }
    </style>
</head>

<body>
  
    <!-- Footer Section -->
    <footer>
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-section about">
                    <h3>About Us</h3>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur et risus vitae velit faucibus
                        volutpat.</p>
                </div>
                <div class="footer-section links">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="#">Home</a></li>
                        <li><a href="#">About</a></li>
                        <li><a href="#">Services</a></li>
                        <li><a href="#">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-section contact">
                    <h3>Contact Us</h3>
                    <p>Email: contact@example.com</p>
                    <p>Phone: +123 456 7890</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 Company Name. All Rights Reserved.</p>
            </div>
        </div>
    </footer>
</body>

</html>
