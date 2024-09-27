<?php
// Start PHP session (if needed for user authentication)
session_start();

// Simulated user data (in a real application, this data would come from a database)
$user = [
    'name' => 'John Doe',
    'email' => 'johndoe@example.com',
    'profile_picture' => '../adminside/images.jpeg', // Ensure you have a profile picture in the same directory
    'bio' => 'Web Developer with a passion for creating interactive applications.',
	'address' => 'Address:- Dipak Nagar Althan Road Pandesara'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .profile-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .profile-header {
            text-align: center;
        }

        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #007bff;
        }

        h1 {
            margin: 10px 0;
        }

        .email {
            color: #666;
            font-size: 14px;
        }

        .profile-bio {
            margin-top: 20px;
        }

        h2 {
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <div class="profile-header">
            <img src="<?php echo $user['profile_picture']; ?>" alt="Profile Picture" class="profile-picture">
            <h1><?php echo $user['name']; ?></h1>
            <p class="email"><?php echo $user['email']; ?></p>
        </div>
        <div class="profile-bio">
            <h2>About Me</h2>
            <p><?php echo $user['bio']; ?></p>
			<p><?php echo $user['address']; ?></p>
        </div>
    </div>
</body>
</html>
