<?php
include 'conn.php';

// Handle form submission to add a new service
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $status = $_POST['status'];

    // Handle file upload
    $image = $_FILES['image'];
    $imagePath = '';

    if ($image['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/'; // Make sure this directory exists and is writable
        $imagePath = $uploadDir . basename($image['name']);

        // Move the uploaded file to the designated directory
        if (move_uploaded_file($image['tmp_name'], $imagePath)) {
            // Prepare and execute the insertion query
            $stmt = $conn->prepare("INSERT INTO admin_services (title, description, image, status) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $title, $description, $imagePath, $status);
            $stmt->execute();

            // Redirect to the same page to avoid form resubmission
            header('Location: services.php');
            exit();
        } else {
            echo "Error uploading file.";
        }
    } else {
        echo "File upload error: " . $image['error'];
    }
}

// Fetch all services from the database
$result = $conn->query("SELECT * FROM admin_services");
$services = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        form {
            margin-bottom: 30px;
            padding: 15px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <h1>Services Management</h1>

    <!-- Form to add a new service -->
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="title">Title:</label>
        <input type="text" name="title" required>
        <br>
        <label for="description">Description:</label>
        <textarea name="description" required></textarea>
        <br>
        <label for="image">Image:</label>
        <input type="file" name="image" accept="image/*" required>
        <br>
        <label for="status">Status:</label>
        <select name="status">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
        <br>
        <button type="submit">Add Service</button>
    </form>

    <!-- Table to display existing services -->
    <h2>Existing Services</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Description</th>
                <th>Image</th>
                <th>Status</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($services as $service): ?>
            <tr>
                <td><?php echo htmlspecialchars($service['id']); ?></td>
                <td><?php echo htmlspecialchars($service['title']); ?></td>
                <td><?php echo htmlspecialchars($service['description']); ?></td>
                <td><img src="<?php echo htmlspecialchars($service['image']); ?>" alt="<?php echo htmlspecialchars($service['title']); ?>" width="100"></td>
                <td><?php echo htmlspecialchars($service['status']); ?></td>
                <td><?php echo htmlspecialchars($service['created_at']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>

<?php
$conn->close();
?>
