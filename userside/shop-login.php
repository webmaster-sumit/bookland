<?php
include 'conn.php';
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collecting data from the form
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Prepared statement to check user credentials
    $stmt = $conn->prepare("SELECT id, email, password FROM users WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($user_id, $user_email, $hashed_password);
        $stmt->fetch();

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            // Start session and store user information
            $_SESSION['user_id'] = $user_id;
            $_SESSION['email'] = $user_email;
            // Redirect to a dashboard or another page
            header("Location: home.php");
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "No account found with that email!";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Add your CSS files here -->
    <link rel="stylesheet" href="path/to/bootstrap.css">
    <link rel="stylesheet" href="path/to/fontawesome.css">
    <link rel="stylesheet" href="css/custom.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <section class="content-inner shop-account">
        <div class="container">
            <form id="login" class="tab-pane active col-12" action="" method="POST">
                <h4 class="text-secondary">LOGIN</h4>
                <p class="font-weight-600">If you have an account with us, please log in.</p>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <div class="mb-4">
                    <label class="label-title">E-MAIL *</label>
                    <input name="email" required class="form-control" placeholder="Your Email Id" type="email">
                </div>
                <div class="mb-4">
                    <label class="label-title">PASSWORD *</label>
                    <input name="password" required class="form-control" placeholder="Type Password" type="password">
                </div>
                <div class="text-left">
                    <button type="submit" class="btn btn-primary btnhover me-2">Login</button>
                    <a href="forgot-password.php" class="m-l5"><i class="fas fa-unlock-alt"></i> Forgot Password</a>
                </div>
            </form>
        </div>
    </section>

    <?php include 'footer.php'; ?>

    <script src="js/jquery.min.js"></script>
    <script src="vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/bootstrap-select/dist/js/bootstrap-select.min.js"></script>
    <script src="js/custom.js"></script>
</body>
</html>
