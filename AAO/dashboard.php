<?php
session_start();

// Check if user is logged in, if not, redirect to login
if (!isset($_SESSION['department_id'])) {
    header('Location: login.php');
    exit;
}

// Retrieve department name or ID
$department_id = $_SESSION['department_id'];

// Optional: You may want to fetch the department name from the database using the department ID
include('dbconn.php');
$query = "SELECT department_name FROM departments WHERE department_id = '$department_id'";
$result = mysqli_query($conn, $query);
$department = mysqli_fetch_assoc($result)['department_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        /* General body styling */
body {
    background-color: linear-gradient(to bottom right, #d4edda, #ffffff); /* Light green background */
    color: #003300; /* Dark green text */
    font-family: Arial, sans-serif;
}

/* Header-Board */
.header-board {
    width: 100%;
    margin: 20px;
    padding: 20px;
    background-color: #ffffff; /* White background for content */
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow */
}


/* Centered text styling */
.text-center {
    color: #004d00; /* Slightly darker green for headings */
    font-weight: bold;
}

/* Buttons (if needed) */
.btn {
    background-color: #007f00; /* Green button background */
    color: white; /* White text */
    border: none;
}

.btn:hover {
    background-color: #005c00; /* Darker green on hover */
}

/* Other reusable elements */
strong {
    color: #004d00; /* Bold text in dark green */
}

    </style>
</head>
<body>
    <!-- Sidebar -->
    <?php include('sidebar.php'); ?>

    <!-- Main content -->
    <div class="header-board">
        <h1 class="text-center">Welcome to the Dashboard</h1>
        <p class="text-center">Logged in as: <strong><?php echo $department; ?></strong></p>
    </div>

    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
