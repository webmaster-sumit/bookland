<?php
// MySQLi Connection
$host = 'localhost'; // Database host (usually 'localhost')
$dbname = 'fasion'; // Name of your database
$user = 'root'; // Your MySQL username
$pass = ''; // Your MySQL password

// Create the connection    
$conn = new mysqli($host, $user, $pass, $dbname);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
