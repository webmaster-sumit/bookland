<?php

// Include the database connection
include 'conn.php';
session_start();

$error = ''; // Initialize an error message variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collecting data from the form
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the email exists in the database
    $stmt = $conn->prepare("SELECT * FROM admins WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Email found, fetch the user data
        $row = $result->fetch_assoc();
        
        // Verify the password
        if (password_verify($password, $row['password'])) {
            // Set session variables
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_username'] = $row['username'];

            // Redirect to the welcome page
            header("Location: welcome.php");
            exit(); // Ensure no further script execution after the redirect
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "No account found with that email!";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - Bookland</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Custom styles */
        html, body {
            height: 100%; /* Set full height */
        }
        body {
            display: flex;
            flex-direction: column; /* Stack children vertically */
        }
        .content {
            flex: 1; /* Take up available space */
        }
        footer {
            background-color: #f8f9fa; /* Footer background color */
            padding: 10px 0; /* Footer padding */
        }
    </style>
</head>
<body>
<?php include 'nav1.php' ?>

<div class="container content">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-7">
            <div class="card shadow mt-5">
                <div class="card-header text-center">
                    <h3>Admin Login</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <form action="adm_login.php" method="POST" id="loginForm">
                        <div class="form-group">
                            <label for="email">Email address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Login</button>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <p><a href="adm_register.php">Don't have an account? Register here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>

<!-- JAVASCRIPT FILES ========================================= -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script>
    // Optional: Prevent form submission if JavaScript is disabled
    $(document).ready(function() {
        $('#loginForm').on('submit', function(e) {
            // Additional validation can go here
            console.log("Form submitted"); // For debugging
        });
    });
</script>
</body>
</html>
