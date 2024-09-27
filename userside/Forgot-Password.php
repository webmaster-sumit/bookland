<?php
include 'conn.php'; // Database connection file

$error = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    
    // Check if the email exists in the users table
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($user_id);
        $stmt->fetch();

        // Create a token for password reset
        $token = bin2hex(random_bytes(50)); // Generating a secure token
        $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token expiration time

        // Insert token into the reset_tokens table
        $insert_stmt = $conn->prepare("INSERT INTO reset_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
        $insert_stmt->bind_param('iss', $user_id, $token, $expires_at);
        $insert_stmt->execute();

        // Here you would send an email with the reset link (not implemented)
        // Example: mail($email, "Password Reset", "Click here to reset your password: http://yourdomain.com/reset_password.php?token=$token");

        $message = "Password reset email sent.";
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
    <title>Forgot Password</title>
</head>
<body>
    <form action="" method="POST">
        <h2>Forgot Password</h2>
        <?php if ($error): ?>
            <div style="color: red;"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($message): ?>
            <div style="color: green;"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <input type="email" name="email" required placeholder="Enter your email">
        <button type="submit">Send Reset Link</button>
    </form>
</body>
</html>
