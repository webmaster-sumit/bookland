<?php
// Enable error reporting


// Include the database connection
include 'conn.php'; // Ensure this connects using MySQLi

// Check if the connection variable is set
if (!$mysqli) {
    die("Database connection not established.");
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

    $stmt = $mysqli->prepare("INSERT INTO slider (name, image, status) VALUES (?, ?, ?)");
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

    $stmt = $mysqli->prepare("UPDATE slider SET name = ?, image = ?, status = ? WHERE id = ?");
    $stmt->bind_param("sssi", $name, $image, $status, $id);
    $stmt->execute();
    $stmt->close();
    header('Location: slider.php');
    exit();
}

// Handle delete
if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    $stmt = $mysqli->prepare("DELETE FROM slider WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header('Location: slider.php');
    exit();
}

// Fetch sliders
$result = $mysqli->query("SELECT * FROM slider");
if ($result === false) {
    die("Error fetching sliders: " . $mysqli->error);
}
$sliders = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slider Management</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
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
        h1 {
            color: #343a40;
        }
        .user-image {
            width: 60px;
            height: 60px;
            border-radius: 8px;
        }
        .status.active {
            color: green;
        }
        .status.inactive {
            color: red;
        }
        .modal-header {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>
<?php include 'nav.php'; ?>
    <div class="container">
        <h1 class="text-center">Slider Management</h1>
        <button class="btn btn-primary mb-3" onclick="openInsertForm()">Add Slider</button>
        <table class="table table-bordered">
            <thead class="thead-light">
                <tr>
                    <th>Name</th>
                    <th>Image</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sliders as $slider): ?>
                <tr data-id="<?php echo $slider['id']; ?>">
                    <td><?php echo htmlspecialchars($slider['name']); ?></td>
                    <td><img src="uploads/<?php echo htmlspecialchars($slider['image']); ?>" alt="<?php echo htmlspecialchars($slider['name']); ?>" class="user-image"></td>
                    <td class="status <?php echo htmlspecialchars($slider['status']); ?>"><?php echo htmlspecialchars(ucfirst($slider['status'])); ?></td>
                    <td>
                        <form method="post" action="" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $slider['id']; ?>">
                            <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($slider['image']); ?>">
                            <button type="button" class="btn btn-warning btn-sm" onclick="openUpdateForm(<?php echo $slider['id']; ?>)">Update</button>
                            <button type="submit" name="delete" class="btn btn-danger btn-sm">Delete</button>
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
                        <button type="button" class="close" onclick="closeUpdateForm()">
                            <span>&times;</span>
                        </button>
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
                        <button type="button" class="close" onclick="closeInsertForm()">
                            <span>&times;</span>
                        </button>
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
        function openUpdateForm(id) {
            const row = document.querySelector(`tr[data-id='${id}']`);
            document.getElementById('updateId').value = id;
            document.getElementById('updateName').value = row.cells[0].innerText;
            document.getElementById('currentImage').value = row.cells[1].querySelector('img').src.split('/').pop();
            document.getElementById('updateStatus').value = row.cells[2].innerText.toLowerCase();
            $('#updateModal').modal('show');
        }

        function closeUpdateForm() {
            $('#updateModal').modal('hide');
        }

        function openInsertForm() {
            $('#insertModal').modal('show');
        }

        function closeInsertForm() {
            $('#insertModal').modal('hide');
        }
    </script>
</body>
</html>
