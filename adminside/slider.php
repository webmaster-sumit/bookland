<?php
session_start();
include 'conn.php';

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: adm_login.php");
    exit();
}

error_reporting(0);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle insert
if (isset($_POST['insert'])) {
    $name = $_POST['name'];
    $image = $_FILES['image']['name'];
    $status = $_POST['status'];

    // Handle image upload
    $targetDir = "uploads/";
    $targetFile = $targetDir . basename($image);
    move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile);

    $stmt = $conn->prepare("INSERT INTO slider (name, image, status) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $image, $status);
    $stmt->execute();
    $stmt->close();
    header('Location: slider.php');
    exit();
}

// Handle update
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $status = $_POST['status'];

    // Use current image if no new upload
    $image = $_FILES['image']['name'] ? $_FILES['image']['name'] : $_POST['current_image'];

    // Handle image upload if a new image is provided
    if ($_FILES['image']['name']) {  
        $targetDir = "uploads/";
        $targetFile = $targetDir . basename($image);
        move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile);
    }

    $stmt = $conn->prepare("UPDATE slider SET name = ?, image = ?, status = ? WHERE id = ?");
    $stmt->bind_param("sssi", $name, $image, $status, $id);
    $stmt->execute();
    $stmt->close();
    header('Location: slider.php');
    exit();
}

// Handle delete
if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    $stmt = $conn->prepare("DELETE FROM slider WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header('Location: slider.php');
    exit();
}

// Fetch sliders (ORDER BY id DESC to get the latest first)
$result2 = "SELECT * FROM slider ORDER BY id DESC";
$query = mysqli_query($conn, $result2);
if ($query === false) {
    die("Error fetching sliders");
}
$sliders = [];
while ($row = mysqli_fetch_assoc($query)) {
    $sliders[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slider Management</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 900px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        .user-image {
            width: 100px; /* Increase image size */
            height: 60px;
            border-radius: 8px;
            object-fit: cover;
        }
        .status.active {
            color: green;
            font-weight: bold;
        }
        .status.inactive {
            color: red;
            font-weight: bold;
        }
        .modal-header {
            background-color: #007bff; /* Bootstrap primary color */
            color: white;
        }
        .modal-footer .btn {
            background-color: #28a745; /* Bootstrap success color */
        }
        .modal-footer .btn:hover {
            background-color: #218838; /* Darker green on hover */
        }
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>
    <div class="container">
        <h1 class="text-center mb-4">Slider Management</h1>
        <button class="btn btn-primary mb-3" onclick="$('#insertModal').modal('show')">
            <i class="fas fa-plus-circle"></i> Add Slider
        </button>
        <table class="table table-bordered">
            <thead class="thead-light">
                <tr>
                    <th>Serial No.</th> <!-- Added for serial number -->
                    <th>Name</th>
                    <th>Image</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $serial_number = 1; // Initialize serial number
                foreach ($sliders as $slider): 
                ?>
                <tr>
                    <td><?php echo $serial_number++; ?></td> <!-- Display serial number -->
                    <td><?php echo htmlspecialchars($slider['name']); ?></td>
                    <td><img src="uploads/<?php echo htmlspecialchars($slider['image']); ?>" alt="<?php echo htmlspecialchars($slider['name']); ?>" class="user-image"></td>
                    <td class="status <?php echo htmlspecialchars($slider['status']); ?>"><?php echo htmlspecialchars(ucfirst($slider['status'])); ?></td>
                    <td>
                        <button type="button" class="btn btn-warning btn-sm" onclick="openUpdateForm(<?php echo htmlspecialchars(json_encode($slider)); ?>)">
                            <i class="fas fa-edit"></i> Update
                        </button>
                        <form method="post" action="" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $slider['id']; ?>">
                            <button type="submit" name="delete" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash-alt"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Update Form Modal -->
        <div id="updateModal" class="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Slider</h5>
                        <button type="button" class="close" onclick="$('#updateModal').modal('hide')">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form method="post" id="updateForm" enctype="multipart/form-data">
                            <input type="hidden" name="id" id="updateId">
                            <div class="form-group">
                                <label for="name">Name:</label>
                                <input type="text" name="name" id="updateName" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="image">Image:</label>
                                <input type="file" name="image" id="updateImage" class="form-control-file">
                                <input type="hidden" name="current_image" id="currentImage">
                            </div>
                            <div class="form-group">
                                <label for="status">Status:</label>
                                <select name="status" id="updateStatus" class="form-control">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <button type="submit" name="update" class="btn btn-primary">Update Slider</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Insert Form Modal -->
        <div id="insertModal" class="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Slider</h5>
                        <button type="button" class="close" onclick="$('#insertModal').modal('hide')">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form method="post" id="insertForm" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="name">Name:</label>
                                <input type="text" name="name" id="insertName" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="image">Image:</label>
                                <input type="file" name="image" id="insertImage" class="form-control-file" required>
                            </div>
                            <div class="form-group">
                                <label for="status">Status:</label>
                                <select name="status" id="insertStatus" class="form-control">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <button type="submit" name="insert" class="btn btn-primary">Add Slider</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function openUpdateForm(slider) {
            $('#updateId').val(slider.id);
            $('#updateName').val(slider.name);
            $('#updateStatus').val(slider.status);
            $('#currentImage').val(slider.image);
            $('#updateModal').modal('show');
        }
    </script>
    <?php include "footer.php"?>
</body>
</html>
