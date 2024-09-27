<?php
// Database configuration
$host = 'localhost';      // Change this to your database host
$username = 'root';       // Change this to your database username
$password = '';           // Change this to your database password
$database = 'fasion';     // Change this to your database name

// Create a connection
$conn = new mysqli($host, $username, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed:" . $conn->connect_error);
}
echo "";
?>
