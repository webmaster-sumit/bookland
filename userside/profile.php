<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // User is not logged in, redirect to login page
    header("Location: shop-login.php");
    exit();
}

// User is logged in, you can display their profile information here
$user_id = $_SESSION['user_id'];
$email = $_SESSION['email'];

// Include the database connection file
require 'conn.php';

// Fetch user data from the database
$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT name, email, phone, address, bio, profile_picture FROM users WHERE id = ?");
$stmt->bind_param("i", $userId); // Bind user_id as an integer
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "User not found!";
    exit();
}

$user = $result->fetch_assoc();
$updated = false; // Flag to check if the profile was updated

// Update user data if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_profile'])) {
        $newName = $_POST['name'];
        $newEmail = $_POST['email'];
        $newPhone = $_POST['phone'];
        $newAddress = $_POST['address'];
        $newBio = $_POST['bio'];

        // Profile picture upload handling
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
            $targetDir = "uploads/";
            
            // Check if the directory exists, if not, create it
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            // Create a unique file name to avoid conflicts
            $fileExtension = strtolower(pathinfo($_FILES["profile_picture"]["name"], PATHINFO_EXTENSION));
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($fileExtension, $allowedTypes)) {
                $newFileName = uniqid() . "." . $fileExtension;
                $targetFile = $targetDir . $newFileName;

                // Move the uploaded file to the target directory
                if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $targetFile)) {
                    $profilePicture = $targetFile; // Update profile picture variable
                } else {
                    echo "Error: Unable to upload the profile picture.";
                    $profilePicture = $user['profile_picture']; // Keep the old picture if upload fails
                }
            } else {
                echo "Error: Only image files (JPG, JPEG, PNG, GIF) are allowed.";
                $profilePicture = $user['profile_picture'];
            }
        } else {
            $profilePicture = $user['profile_picture']; // Keep the old picture if no new picture is uploaded
        }

        // Update the user information in the database
        $updateStmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone = ?, address = ?, bio = ?, profile_picture = ? WHERE id = ?");
        $updateStmt->bind_param("ssssssi", $newName, $newEmail, $newPhone, $newAddress, $newBio, $profilePicture, $userId);
        
        if ($updateStmt->execute()) {
            $_SESSION['name'] = $newName; // Update session variable
            $updated = true; // Set the updated flag to true
            // Fetch updated user data
            $stmt->execute(); // Re-execute to get the latest user info
            $result = $stmt->get_result();
            $user = $result->fetch_assoc(); // Get the updated user info
        } else {
            echo "Error updating profile!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .profile-picture {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
        }
        .container {
            margin-bottom: 100px; /* Increased bottom margin for more space before footer */
        }
    </style>
</head>
<body>
    <?php include 'navbar2.php'; ?> <!-- Include the Navbar -->

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-4 text-center">
                <img src="<?php echo htmlspecialchars(!empty($user['profile_picture']) ? $user['profile_picture'] : 'default.png'); ?>" class="profile-picture mb-3" alt="Profile Picture">
                <h4><?php echo htmlspecialchars($user['name']); ?></h4>
                <p><?php echo htmlspecialchars($user['email']); ?></p>
            </div>
            <div class="col-md-8">
                <?php if ($updated): ?>
                    <div class="alert alert-success" role="alert">
                        Profile updated successfully!
                    </div>
                <?php endif; ?>

                <h2>User Details</h2>
                <div class="user-details">
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                    <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
                    <p><strong>Address:</strong> <?php echo htmlspecialchars($user['address']); ?></p>
                    <p><strong>Bio:</strong> <?php echo htmlspecialchars($user['bio']); ?></p>
                </div>
                
                <h2>Edit Profile</h2>
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($user['address']); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="bio" class="form-label">Bio</label>
                        <textarea class="form-control" id="bio" name="bio" rows="3"><?php echo htmlspecialchars($user['bio']); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="profile_picture" class="form-label">Profile Picture</label>
                        <input type="file" class="form-control" id="profile_picture" name="profile_picture">
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary" name="update_profile">Update Profile</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?> <!-- Include the Footer -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
