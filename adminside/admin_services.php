<?php
session_start();
include 'conn.php';

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: adm_login.php");
    exit();
}


// Delete request handle kar rahe hain
if (isset($_GET['delete_id'])) {
    $deleteId = intval($_GET['delete_id']);
    
    // Delete statement prepare kiya
    $stmt = $conn->prepare("DELETE FROM admin_services WHERE id = ?");
    $stmt->bind_param("i", $deleteId);
    
    if ($stmt->execute()) {
        // Successfully delete ho gaya
        header('Location: admin_services.php'); // Redirect taaki resubmission avoid ho
        exit();
    } else {
        // Agar delete mein error aaye toh
        echo "Error deleting service: " . $conn->error;
    }
    $stmt->close();
}

// Form submission handle kar rahe hain service add/update ke liye
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $status = $_POST['status'];

    // File upload handle kar rahe hain
    $imagePath = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image = $_FILES['image'];
        $uploadDir = 'uploads/'; // Yeh directory hona chahiye aur writable bhi

        // File name ko sanitize kar rahe hain taaki security issue na ho
        $imageName = basename($image['name']);
        $imageName = preg_replace("/[^a-zA-Z0-9\._-]/", "_", $imageName);
        $imagePath = $uploadDir . $imageName;

        // File ko move kar rahe hain designated directory mein
        move_uploaded_file($image['tmp_name'], $imagePath);
    }

    // Dekhte hain update karna hai ya add karna hai
    if (isset($_POST['id']) && $_POST['id'] != '') {
        // Existing service update kar rahe hain
        $serviceId = intval($_POST['id']);

        // Agar naya image upload ho, toh usko update karo; nahi toh purana rakh lo
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
        // New service add kar rahe hain
        $stmt = $conn->prepare("INSERT INTO admin_services (title, description, image, status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $title, $description, $imagePath, $status);
        $stmt->execute();
        $stmt->close();
    }
    
    // Redirect to service page
    header('Location: admin_services.php');
    exit();
}

// Database se saare services fetch kar rahe hain
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

        <!-- Form service add/update karne ke liye -->
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
            <input type="hidden" name="id" value=""> <!-- Hidden field service ID ke liye -->
            <button type="submit" class="btn btn-primary">Save Service</button>
        </form>

        <!-- Table services ko display karne ke liye -->
        <table class="table table-bordered table-striped mt-4">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
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
                    <?php foreach ($services as $service): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($service['id']); ?></td>
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
                            <a href="?delete_id=<?php echo htmlspecialchars($service['id']); ?>" class="btn btn-danger button" onclick="return confirm('Kya aap sach mein is service ko delete karna chahte hain?');">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">Filhaal koi services available nahi hain.</td>
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
