<?php
// Database configuration
$host = 'localhost';
$username = 'root'; // Default XAMPP MySQL username
$password = '';     // Default XAMPP MySQL password (empty)
$database = 'bloodbank';

// Create connection
$conn = new mysqli($host, $username, $password, $database, 3307);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to handle special characters
$conn->set_charset("utf8mb4");
?>
