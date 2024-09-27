<?php
include 'conn.php';

$error = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $new_password = $_POST['new_password'];
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT); // Hash the new password

    // Validate token and get user ID
    $stmt = $conn->prepare("SELECT user_id FROM reset_tokens WHERE token = ? AND expires_at > NOW()");
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();

    if ($user_id) {
        // Update the user's password
        $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $update_stmt->bind_param('si', $hashed_password, $user_id);
        $update_stmt->execute();

        // Optionally delete the token after use
        $delete_stmt = $conn->prepare("DELETE FROM reset_tokens WHERE token = ?");
        $delete_stmt->bind_param('s', $token);
        $delete_stmt->execute();

        $message = "Your password has been reset successfully.";
    } else {
        $error = "Invalid or expired token!";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reset Password</title>
</head>
<body>
    <form action="" method="POST">
        <h2>Reset Password</h2>
        <?php if ($error): ?>
            <div style="color: red;"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($message): ?>
            <div style="color: green;"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
        <input type="password" name="new_password" required placeholder="Enter new password">
        <button type="submit">Reset Password</button>
    </form>
</body>
</html>
