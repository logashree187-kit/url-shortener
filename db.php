<?php
// db.php - Database connection file

// Database credentials
$host     = "localhost";
$username = "root";
$password = "";          // Default XAMPP MySQL password is empty
$database = "url_shortener";

// Create connection using MySQLi
$conn = new mysqli($host, $username, $password, $database);

// Check if the connection failed
if ($conn->connect_error) {
    // Stop script and display error message
    die("Database connection failed: " . $conn->connect_error);
}

// Set character set to utf8mb4 for universal character support
$conn->set_charset("utf8mb4");
?>