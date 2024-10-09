<?php
session_start();
include 'conn.php';

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: adm_login.php");
    exit();
}

// Delete request handling
if (isset($_GET['delete_id'])) {
    $deleteId = intval($_GET['delete_id']);
    
    // Prepare delete statement
    $stmt = $conn->prepare("DELETE FROM admin_services WHERE id = ?");
    $stmt->bind_param("i", $deleteId);
    
    if ($stmt->execute()) {
        // Successfully deleted
        header('Location: admin_services.php'); // Redirect to avoid form resubmission
        exit();
    } else {
        // Error occurred during deletion
        echo "Error deleting service: " . $conn->error;
    }
    $stmt->close();
}

// Handle form submission for adding/updating a service
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $status = $_POST['status'];

    // Handle file upload
    $imagePath = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image = $_FILES['image'];
        $uploadDir = 'uploads/'; // Make sure this directory exists and is writable

        // Sanitize the file name to prevent security issues
        $imageName = basename($image['name']);
        $imageName = preg_replace("/[^a-zA-Z0-9\._-]/", "_", $imageName);
        $imagePath = $uploadDir . $imageName;

        // Move the file to the designated directory
        move_uploaded_file($image['tmp_name'], $imagePath);
    }

    // Check if updating an existing service or adding a new one
    if (isset($_POST['id']) && $_POST['id'] != '') {
        // Update existing service
        $serviceId = intval($_POST['id']);

        // If a new image is uploaded, update it, otherwise keep the old one
        if ($imagePath != '') {
            $stmt = $conn->prepare("UPDATE admin_services SET title = ?, description = ?, image = ?, status = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $title, $description, $imagePath, $status, $serviceId);
        } else {
            $stmt = $conn->prepare("UPDATE admin_services SET title = ?, description = ?, status = ? WHERE id = ?");
            $stmt->bind_param("sssi", $title, $description, $status, $serviceId);
        }
        $stmt->execute();
        $stmt->close();
    } else {
        // Add new service
        $stmt = $conn->prepare("INSERT INTO admin_services (title, description, image, status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $title, $description, $imagePath, $status);
        $stmt->execute();
        $stmt->close();
    }
    
    // Redirect to service page
    header('Location: admin_services.php');
    exit();
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
    <title>Service Management</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        img {
            max-width: 100px;
            height: auto;
        }
        .button {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <?php include "nav.php"?>
    <div class="container">
        <h1 class="my-4">Service Management</h1>

        <!-- Form for adding/updating service -->
        <form action="" method="POST" enctype="multipart/form-data" class="border p-4 bg-light rounded">
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea name="description" class="form-control" required></textarea>
            </div>
            <div class="form-group">
                <label for="image">Image:</label>
                <input type="file" name="image" class="form-control-file" accept="image/*">
            </div>
            <div class="form-group">
                <label for="status">Status:</label>
                <select name="status" class="form-control">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <input type="hidden" name="id" value=""> <!-- Hidden field for service ID -->
            <button type="submit" class="btn btn-primary">Save Service</button>
        </form>

        <!-- Table for displaying services -->
        <table class="table table-bordered table-striped mt-4">
            <thead class="thead-dark">
                <tr>
                    <th>Serial No.</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Image</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($services) > 0): ?>
                    <?php 
                    $serial_no = count($services); // Start serial number with the total count of services
                    foreach ($services as $service): ?>
                    <tr>
                        <td><?php echo $serial_no--; ?></td> <!-- Serial number in descending order -->
                        <td><?php echo htmlspecialchars($service['title']); ?></td>
                        <td><?php echo htmlspecialchars($service['description']); ?></td>
                        <td>
                            <?php if ($service['image']): ?>
                                <img src="<?php echo htmlspecialchars($service['image']); ?>" alt="<?php echo htmlspecialchars($service['title']); ?>">
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($service['status']); ?></td>
                        <td><?php echo htmlspecialchars($service['created_at']); ?></td>
                        <td>
                            <a href="javascript:void(0);" class="btn btn-warning button" onclick="populateForm(<?php echo htmlspecialchars(json_encode($service)); ?>)">Update</a>
                            <a href="?delete_id=<?php echo htmlspecialchars($service['id']); ?>" class="btn btn-danger button" onclick="return confirm('Are you sure you want to delete this service?');">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">No services available.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        function populateForm(service) {
            document.querySelector('input[name="title"]').value = service.title;
            document.querySelector('textarea[name="description"]').value = service.description;
            document.querySelector('select[name="status"]').value = service.status;
            document.querySelector('input[name="id"]').value = service.id;
        }
    </script>
    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
<?php include "footer.php"?>
<?php
$conn->close();
?>
