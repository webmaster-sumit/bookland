<!-- footer.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 20px 0;
            width: 100%; /* Ensure footer takes full width */
            position: relative; /* Changed from fixed to relative */
        }

        footer p {
            margin: 0;
            font-size: 14px;
        }

        footer a {
            color: #fff;
            text-decoration: none;
            margin: 0 10px;
        }

        footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <footer>
        <p>Admin Panel &copy; <?php echo date("Y"); ?>. All rights reserved.</p>
        <p>
            <a href="privacy_policy.php">Privacy Policy</a> | 
            <a href="terms_of_service.php">Terms of Service</a> |
            <a href="contact.php">Contact</a>
        </p>
    </footer>

</body>
</html>
