<?php
// Include your database connection setup
include 'conn.php';

$error = '';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data with validation
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    // Check if any field is empty
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        // Check if passwords match
        $error = "Passwords do not match.";
    } else {
        // Check if email already exists
        $sql_check = "SELECT * FROM users WHERE email = ?";
        if ($stmt_check = $conn->prepare($sql_check)) {
            // Bind the email parameter
            $stmt_check->bind_param("s", $email);

            // Execute the query
            $stmt_check->execute();
            $stmt_check->store_result();

            // If the email exists, show an error
            if ($stmt_check->num_rows > 0) {
                $error = "This email address is already registered.";
            } else {
                // Hash the password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Prepare SQL statement to insert the new user
                $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";

                if ($stmt = $conn->prepare($sql)) {
                    // Bind parameters (s = string)
                    $stmt->bind_param("sss", $name, $email, $hashed_password);

                    // Execute the statement
                    if ($stmt->execute()) {
                        // Redirect to the login page on success
                        header("Location: shop-login.php");
                        exit();
                    } else {
                        $error = "Error: " . $stmt->error;
                    }

                    // Close the statement
                    $stmt->close();
                } else {
                    $error = "Error preparing statement: " . $conn->error;
                }
            }

            // Close the email check statement
            $stmt_check->close();
        } else {
            $error = "Error preparing email check statement: " . $conn->error;
        }
    }

    // Close connection
    $conn->close();
}
?>

<!-- Your HTML for registration page goes here -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords" content="" />
    <meta name="author" content="" />
    <meta name="robots" content="" />
    <meta name="description" content="Bookland-Book Store Ecommerce Website" />
    <meta property="og:title" content="Bookland-Book Store Ecommerce Website" />
    <meta property="og:description" content="Bookland-Book Store Ecommerce Website" />
    <meta property="og:image" content="../../makaanlelo.com/tf_products_007/bookland/xhtml/social-image.html" />
    <meta name="format-detection" content="telephone=no">

    <!-- FAVICONS ICON -->
    <link rel="shortcut icon" type="image/x-icon" href="images/favicon.png" />

    <!-- PAGE TITLE HERE -->
    <title>Bookland-Book Store Ecommerce Website</title>

    <!-- MOBILE SPECIFIC -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- STYLESHEETS -->
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="icons/fontawesome/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/bootstrap-select/dist/css/bootstrap-select.min.css">

    <!-- GOOGLE FONTS-->
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700;800&amp;family=Poppins:wght@100;200;300;400;500;600;700;800;900&amp;display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php' ?>
    <div class="page-wraper">
        <div id="loading-area" class="preloader-wrapper-1">
            <div class="preloader-inner">
                <div class="preloader-shade"></div>
                <div class="preloader-wrap"></div>
                <div class="preloader-wrap wrap2"></div>
                <div class="preloader-wrap wrap3"></div>
                <div class="preloader-wrap wrap4"></div>
                <div class="preloader-wrap wrap5"></div>
            </div>
        </div>

        <!-- Registration Form Section -->
        <section class="content-inner">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-6 col-md-8">
                        <div class="card shadow">
                            <div class="card-header text-center">
                                <h3>Register Your Account</h3>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($error)): ?>
                                    <div class="alert alert-danger"><?php echo $error; ?></div>
                                <?php endif; ?>
                                <form action="shop-registration.php" method="POST">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Full Name</label>
                                        <input type="text" class="form-control" id="name" name="name" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email address</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="password" name="password" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Confirm Password</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                    </div>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary">Register</button>
                                    </div>
                                </form>
                            </div>
                            <div class="card-footer text-center">
                                <p>Already have an account? <a href="shop-login.php">Login here</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Registration Form Section End -->
    </div>

    <?php include 'footer.php' ?>

    <!-- JAVASCRIPT FILES ========================================= -->
    <script src="js/jquery.min.js"></script><!-- JQUERY MIN JS -->
    <script src="vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script><!-- BOOTSTRAP MIN JS -->
    <script src="vendor/bootstrap-select/dist/js/bootstrap-select.min.js"></script><!-- BOOTSTRAP SELECT MIN JS -->
    <script src="js/custom.js"></script><!-- CUSTOM JS -->
</body>
</html>
