<?php
include 'conn.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all products
$query = "SELECT * FROM products ORDER BY id DESC";
$result = $conn->query($query);

if (!$result) {
    die("Database query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Catalog</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            padding-top: 20px;
            background-color: #f8f9fa;
        }

        .book-card {
            border: none;
            border-radius: 10px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.2);
        }

        .book-media img {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }

        .book-content {
            padding: 20px;
            text-align: center;
        }

        .book-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .book-title a {
            color: #333;
            text-decoration: none;
        }

        .book-title a:hover {
            color: #007bff;
        }

        .book-price {
            font-size: 18px;
            color: #007bff;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .book-description {
            font-size: 14px;
            color: #555;
            margin-bottom: 10px;
        }

        .book-details ul {
            padding: 0;
            list-style: none;
            font-size: 13px;
            color: #666;
            text-align: left;
        }

        .book-details li {
            margin-bottom: 5px;
        }

        .book-tags {
            padding: 0;
            list-style: none;
            margin: 15px 0;
            text-align: left;
        }

        .book-tags li {
            display: inline-block;
            margin-right: 5px;
        }

        .book-tags a {
            font-size: 12px;
            color: #fff;
            background-color: #007bff;
            padding: 5px 10px;
            border-radius: 4px;
            text-decoration: none;
        }

        .book-tags a:hover {
            background-color: #0056b3;
        }

        .add-to-cart-btn {
            width: 100%;
            background-color: #28a745;
            color: white;
            font-weight: 600;
            transition: background-color 0.3s ease;
            border: none;
            padding: 10px;
        }

        .add-to-cart-btn:hover {
            background-color: #218838;
        }

        /* Media Queries for Mobile Responsiveness */
        @media (max-width: 767.98px) {
            .book-title {
                font-size: 18px;
            }

            .book-price {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
<?php include 'navbar2.php'; ?>

<div class="container">
    <h1 class="text-center mb-4">Books Catalog</h1>
    <div class="row">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="col-md-4 col-sm-6 mb-4">
                <div class="book-card">
                    <a href="product-details.php?id=<?php echo $row['id']; ?>" class="book-media">
                        <?php
                        $imageSrc = '../adminside/' . $row['image'];
                        if (@file_exists($imageSrc) && !empty($row['image'])): ?>
                            <img src="<?php echo htmlspecialchars($imageSrc, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                        <?php else: ?>
                            <img src="path/to/placeholder-image.jpg" alt="No Image Available">
                        <?php endif; ?>
                    </a>
                    <div class="book-content">
                        <h4 class="book-title">
                            <a href="product-details.php?id=<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['name']); ?></a>
                        </h4>
                        <div class="book-price">₹<?php echo number_format($row['price'], 2); ?></div>
                        <div class="book-description">
                            <?php echo htmlspecialchars(substr($row['description'], 0, 100)) . '...'; ?>
                        </div>
                        <div class="book-details">
                            <ul>
                                <li><strong>Author:</strong> <?php echo htmlspecialchars($row['author'] ?? 'Unknown'); ?></li>
                                <li><strong>Publisher:</strong> <?php echo htmlspecialchars($row['publisher'] ?? 'Unknown'); ?></li>
                                <li><strong>Year:</strong> <?php echo htmlspecialchars($row['year'] ?? 'Unknown'); ?></li>
                            </ul>
                        </div>
                        <div class="add-to-cart">
                            <a href="shop-cart.php?view=cart&id=<?php echo $row['id']; ?>" class="btn btn-success add-to-cart-btn">Add to Cart</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>
<?php include 'footer.php'?>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
