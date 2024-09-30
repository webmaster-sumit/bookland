<?php
session_start();

// Include the database connection file
require 'conn.php';

// Check if the user is authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if user is not authenticated
    exit();
}

// Fetch user data from the database
$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $userId); // Bind user_id as an integer
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "User not found!";
    exit();
}

$user = $result->fetch_assoc();

// Update user data if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $newName = $_POST['name'];
    $newEmail = $_POST['email'];

    // Update the user information in the database
    $updateStmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
    $updateStmt->bind_param("ssi", $newName, $newEmail, $userId);
    
    if ($updateStmt->execute()) {
        $_SESSION['name'] = $newName; // Update session variable
        header('Location: profile.php'); // Redirect to profile page after successful update
        exit();
    } else {
        echo "Error updating profile!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar2.php'; ?> <!-- Include the Navbar -->

    <div class="container mt-5">
        <h2 class="text-center">Edit Profile</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Update Profile</button>
            </div>
        </form>
    </div>

    <?php include 'footer.php'; ?> <!-- Include the Footer -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
