<?php
include 'conn.php'; // Adjust the path based on your directory structure

// Fetch active sliders
$result = $conn->query("SELECT * FROM slider WHERE status = 'active'");
$sliders = $result->fetch_all(MYSQLI_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">

<!-- Mirrored from bookland.dexignzone.com/xhtml/index-2.php by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 09 Sep 2024 10:11:42 GMT -->
<head>
	
	<!-- Meta -->
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="keywords" content="" />
	<meta name="author" content="DexignZone" />
	<meta name="robots" content="" />
	<meta name="description" content="Bookland-Book Store Ecommerce Website"/>
	<meta property="og:title" content="Bookland-Book Store Ecommerce Website"/>
	<meta property="og:description" content="IBookland-Book Store Ecommerce Website"/>
	<meta property="og:image" content="../../makaanlelo.com/tf_products_007/bookland/xhtml/social-image.html"/>
	<meta name="format-detection" content="telephone=no">
	
	<!-- FAVICONS ICON -->
	<link rel="icon" type="image/x-icon" href="images/favicon.png"/>
	
	<!-- PAGE TITLE HERE -->
	<title>Bookland Book Store Ecommerce Website</title>
	
	<!-- MOBILE SPECIFIC -->
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<!-- STYLESHEETS -->
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="icons/fontawesome/css/all.min.css">
	<link rel="stylesheet" type="text/css" href="vendor/swiper/swiper-bundle.min.css">
	<link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
	<link rel="stylesheet" type="text/css" href="vendor/bootstrap-select/dist/css/bootstrap-select.min.css">	
	<!-- GOOGLE FONTS-->
	<link rel="preconnect" href="https://fonts.googleapis.com/">
	<link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700;800&amp;family=Poppins:wght@100;200;300;400;500;600;700;800;900&amp;display=swap" rel="stylesheet">

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .slider-caption {
            position: absolute;
            bottom: 20px;
            left: 20px;
            color: white;
            background: rgba(0, 0, 0, 0.5);
            padding: 10px;
            border-radius: 5px;
        }
        .carousel-item {
            height: 600px; /* Set a fixed height for the carousel */
        }
        .carousel-item img {
            height: 100%; /* Full height */
            width: 100%; /* Full width */
            object-fit: cover; /* Cover the carousel item area */
        }
    </style>
</head>
<body>
     <?php include 'navbar.php'; ?>
    <div class="container-fluid">

        <div id="sliderCarousel" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
                <?php if ($sliders): ?>
                    <?php foreach ($sliders as $index => $slider): ?>
                        <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                            <img src="../adminside/uploads/<?php echo htmlspecialchars(string: $slider['image']); ?>" class="d-block w-100" alt="<?php echo htmlspecialchars($slider['name']); ?>">
                            <div class="slider-caption">
                                <h2><?php echo htmlspecialchars($slider['name']); ?></h2>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="carousel-item active">
                        <img src="../adminside/uploads/default.jpg" class="d-block w-100" alt="No slider available">
                        <div class="slider-caption">
                            <h2>No sliders available.</h2>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <a class="carousel-control-prev" href="#sliderCarousel" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#sliderCarousel" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>
    </div>
    <?php include 'footer.php'; ?>
    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
