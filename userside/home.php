<?php

// Include the database connection
include 'conn.php';


?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="keywords" content="" />
	<meta name="author" content="" />
	<meta name="robots" content="" />
	<meta name="description" content="Bookland-Book Store Ecommerce Website"/>
	<meta property="og:title" content="Bookland-Book Store Ecommerce Website"/>
	<meta property="og:description" content="Bookland-Book Store Ecommerce Website"/>
	<meta property="og:image" content="../../makaanlelo.com/tf_products_007/bookland/xhtml/social-image.html"/>
	<meta name="format-detection" content="telephone=no">
	
	<!-- FAVICONS ICON -->
	<link rel="shortcut icon" type="image/x-icon" href="../adminside/images/favicon.png" />
	
	<!-- PAGE TITLE HERE -->
	<title>Bookland-Book Store Ecommerce Website</title>
	
	<!-- MOBILE SPECIFIC -->
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<!-- STYLESHEETS -->
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="icons/fontawesome/css/all.min.css">
	<link rel="stylesheet" type="text/css" href="vendor/bootstrap-select/dist/css/bootstrap-select.min.css">

	<!-- GOOGLE FONTS-->
	<link rel="preconnect" href="https://fonts.googleapis.com/">
	<link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700;800&amp;family=Poppins:wght@100;200;300;400;500;600;700;800;900&amp;display=swap" rel="stylesheet">

</head>
<?php include 'navbar.php'?>
<body id="bg">
<div class="page-wraper">
	<div id="loading-area" class="preloader-wrapper-1">
		<div class="preloader-inner">
			<div class="preloader-shade"></div>
			<div class="preloader-wrap"></div>
			<div class="preloader-wrap wrap2"></div>
			<div class="preloader-wrap wrap3"></div>
			<div class="preloader-wrap wrap4"></div>
			<div class="preloader-wrap wrap5"></div>
		</div> 
	</div>
	
    <!-- Content -->
    <div class="page-content bg-white">
		<!-- contact area -->
        <div class="content-block">
			<!-- Browse Jobs -->
			<section class="content-inner bg-white">
				<div class="container">
					<div class="row">
						<div class="col-xl-3 col-lg-4 m-b30">
							<div class="sticky-top">
								<div class="shop-account">
									<div class="account-detail text-center">
										<div class="my-image">
											<a href="javascript:void(0);">
												<img alt="" src="../adminside/images/profile3.jpg">
											</a>
										</div>
										<div class="account-title">
											<div class="">
												<h4 class="m-b5"><a href="javascript:void(0);">David Matin</a></h4>
												<p class="m-b0"><a href="javascript:void(0);">Web developer</a></p>
											</div>
										</div>
									</div>
									<ul class="account-list">
										<li>
											<a href="my-profile.php" class="active"><i class="far fa-user" aria-hidden="true"></i> 
											<span>Profile</span></a>
										</li>
										<li>
											<a href="shop-cart.php"><i class="flaticon-shopping-cart-1"></i>
											<span>My Cart</span></a>
										</li>
										<li>
											<a href="wishlist.php"><i class="far fa-heart" aria-hidden="true"></i> 
											<span>Wishlist</span></a>
										</li>
										<li>
											<a href="shop.php"><i class="fa fa-briefcase" aria-hidden="true"></i> 
											<span>Shop</span></a>
										</li>
										<li>
											<a href="services.php"><i class="far fa-bell" aria-hidden="true"></i> 
											<span>Services</span></a>
										</li>
										<li>
											<a href="help-desk.php"><i class="far fa-id-card" aria-hidden="true"></i> 
											<span>Help Desk</span></a>
										</li>
										<li>
											<a href="privacy-policy.php"><i class="fa fa-key" aria-hidden="true"></i> 
											<span>Privacy Policy</span></a>
										</li>
										<li>
											<a href="shop-login.php"><i class="fas fa-sign-out-alt" aria-hidden="true"></i> 
											<span>Log Out</span></a>
										</li>
									</ul>
								</div>
							</div>
						</div>
						<div class="col-xl-9 col-lg-8 m-b30">
							<div class="shop-bx shop-profile">
								<div class="shop-bx-title clearfix">
									<h5 class="text-uppercase">Basic Information</h5>
								</div>
								<form>
									<div class="row m-b30">
										<div class="col-lg-6 col-md-6">
											<div class="mb-3">
												<label for="formcontrolinput1" class="form-label">Your Name:</label>
												<input type="text" class="form-control" id="formcontrolinput1" placeholder="Alexander Weir">
											</div>
										</div>
										<div class="col-lg-6 col-md-6">
											<div class="mb-3">
												<label for="formcontrolinput2" class="form-label">Professional title:</label>
												<input type="text" class="form-control" id="formcontrolinput2" placeholder="Web Designer">
											</div>
										</div>
										<div class="col-lg-6 col-md-6">
											<div class="mb-3">
												<label for="formcontrolinput3" class="form-label">Languages:</label>
												<input type="text" class="form-control" id="formcontrolinput3" placeholder="Language">
											</div>
										</div>
										<div class="col-lg-6 col-md-6">
											<div class="mb-3">
												<label for="formcontrolinput4" class="form-label">Age:</label>
												<input type="text" class="form-control" id="formcontrolinput4" placeholder="Age">
											</div>
										</div>
										<div class="col-lg-12 col-md-12">
											
											<div class="mb-3">
												<label for="exampleFormControlTextarea" class="form-label">Description:</label>
												<textarea class="form-control" id="exampleFormControlTextarea" rows="5">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s.</textarea>
											</div>
										</div>
									</div>
									<div class="shop-bx-title clearfix">
										<h5 class="text-uppercase">Contact Information</h5>
									</div>
									<div class="row">
										<div class="col-lg-6 col-md-6">
											<div class="mb-3">
												<label for="formcontrolinput5" class="form-label">Contact Number:</label>
												<input type="text" class="form-control" id="formcontrolinput5" placeholder="+1 123 456 7890">
											</div>
										</div>
										<div class="col-lg-6 col-md-6">
											<div class="mb-3">
												<label for="formcontrolinput6" class="form-label">Email Address:</label>
												<input type="text" class="form-control" id="formcontrolinput6" placeholder="info@example.com">
											</div>
										</div>
										<div class="col-lg-6 col-md-6">
											<div class="mb-3">
												<label for="formcontrolinput7" class="form-label">Country:</label>
												<input type="text" class="form-control" id="formcontrolinput7" placeholder="Country Name">
											</div>
										</div>
										<div class="col-lg-6 col-md-6">
											<div class="mb-3">
												<label for="formcontrolinput8" class="form-label">Postcode:</label>
												<input type="text" class="form-control" id="formcontrolinput8" placeholder="112233">
											</div>
										</div>
										<div class="col-lg-6 col-md-6">
											<div class="mb-3">
												<label for="formcontrolinput9" class="form-label">City:</label>
												<input type="text" class="form-control" id="formcontrolinput9" placeholder="City Name">
											</div>
										</div>
										<div class="col-lg-6 col-md-6">
											<div class="mb-4">
												<label for="formcontrolinput10" class="form-label">Full Address:</label>
												<input type="text" class="form-control" id="formcontrolinput10" placeholder="New york City">
											</div>
										</div>
									</div>
									<button class="btn btn-primary btnhover">Save Setting</button>
								</form>
							</div>    
						</div>
					</div>
				</div>
			</section>
            <!-- Browse Jobs END -->
		</div>
    </div>
    <!-- Content END-->
	
    <button class="scroltop" type="button"><i class="fas fa-arrow-up"></i></button>
</div>
<?php include 'footer.php'?>
<!-- JAVASCRIPT FILES ========================================= -->
<script src="js/jquery.min.js"></script><!-- JQUERY MIN JS -->
<script src="vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script><!-- BOOTSTRAP MIN JS -->
<script src="vendor/bootstrap-select/dist/js/bootstrap-select.min.js"></script><!-- BOOTSTRAP SELECT MIN JS -->
<script src="js/custom.js"></script><!-- CUSTOM JS -->

</body>

<!-- Mirrored from bookland.dexignzone.com/xhtml/my-profile.php by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 09 Sep 2024 10:11:47 GMT -->
</html>