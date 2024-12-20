<?php

// Database configuration
$host = "localhost";      // Hostname
$username = "root";       // MySQL username
$password = "";           // MySQL password
$database = "no_due_form_system"; // Replace with your database name

// Create a new MySQLi connection
$conn = new mysqli($host, $username, $password, $database);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
