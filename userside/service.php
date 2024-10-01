<?php
include 'conn.php';

// Fetching only active services from the database
$result = $conn->query("SELECT * FROM admin_services WHERE status = 'active'");
$services = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Services</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f0f0f0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .service-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
        }

        .service-item {
            width: calc(33.333% - 20px);
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #fff;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .service-item:hover {
            transform: translateY(-5px);
        }

        .service-image {
            width: 100%;
            height: 200px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .service-image img {
            width: 90%;
            height: 90%;
            object-fit: cover;
        }

        .service-content {
            padding: 15px;
        }

        .service-content h2 {
            font-size: 1.5em;
            margin-bottom: 10px;
            color: #007BFF;
        }

        .service-content p {
            font-size: 1em;
            line-height: 1.5;
            color: #666;
        }

        .add-to-cart-button {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 15px;
            background-color: #28a745;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .add-to-cart-button:hover {
            background-color: #218838;
        }
    </style>
</head>

<body>
    <?php include "navbar.php"; ?>

    <div class="container">
        <h1>Our Services</h1>

        <!-- Services List -->
        <div class="service-list">
            <?php if (count($services) > 0): ?>
               
                <?php foreach ($services as $service): ?>
                    <div class="service-item">
                        <?php if ($service['image']): ?>
                            <div class="service-image">
                                <img src="<?php echo htmlspecialchars('../adminside/' . $service['image']); ?>" alt="<?php echo htmlspecialchars($service['title']); ?>">
                            </div>
                        <?php endif; ?>
                        <div class="service-content">
                            <h2><?php echo htmlspecialchars($service['title']); ?></h2>
                            <p><?php echo nl2br(htmlspecialchars($service['description'])); ?></p>
                            <!-- Add to Cart Button -->
                            <a href="product.php?service_id=<?php echo htmlspecialchars($service['id']); ?>" class="add-to-cart-button">Add to Cart</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No services are currently available. Please check back later!</p>
            <?php endif; ?>
        </div>
    </div>

    <?php include "footer.php"; ?>

    <?php $conn->close(); ?>
</body>

</html>