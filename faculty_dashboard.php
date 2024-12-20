<?php
session_start();

// Check if user is logged in, if not, redirect to login
if (!isset($_SESSION['employee_id'])) {
    header('Location: faculty_login.php');
    exit;
}

// Retrieve employee ID from session
$employee_id = $_SESSION['employee_id'];

// Fetch employee details using the employee ID
include('dbconn.php');
$query = "SELECT name FROM faculty WHERE employee_id = '$employee_id'";
$result = mysqli_query($conn, $query);

// Ensure the query succeeded
if ($result && mysqli_num_rows($result) > 0) {
    $employee = mysqli_fetch_assoc($result);
    $employee_name = $employee['name']; // Retrieve the employee's name
} else {
    $employee_name = 'Unknown'; // Fallback if no record is found
}
$timeout_duration = 300;
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    // Destroy session and redirect to index.php
    session_unset();
    session_destroy();
    header('Location: index.php');
    exit;
}

// Update last activity timestamp
$_SESSION['LAST_ACTIVITY'] = time();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        /* General body styling */
        body {
            background: linear-gradient(to bottom right, #c3e6cb, #ffffff);
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover;
            font-family: Arial, sans-serif;
        }

        .ex {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
            color: #d45454;
            margin-left: 10%;
        }

        /* Header-Board */
        .header-board {
            width: 100%;
            margin: 20px;
            padding: 20px;
            margin-left: 6%;
            border-radius: 10px;
        }

        /* Centered text styling */
        .text-center {
            color: black;
            font-weight: bold;
        }

        /* Buttons (if needed) */
        .btn {
            background-color: #007f00;
            color: white;
            border: none;
        }

        .btn:hover {
            background-color: #005c00;
        }

        /* Other reusable elements */
        strong {
            color: red;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <?php include('faculty_sidebar.php'); ?>

    <!-- Main content -->
    <div class="ex">
        <img src="Mits_logo_24-removebg-preview.png" alt="MITS Logo" height="200" width="800">
    </div>
    <div class="header-board">
        <h1 style="color:#357EC7; font-family: Poppins;" class="text-center">Welcome to the Dashboard</h1>
        <p style="color:#357EC7; font-family: Poppins;" class="text-center">Logged in Faculty as: <strong><?php echo $employee_name; ?></strong></p>
    </div>

    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script>
setTimeout(() => {
        // Redirect to index.php
        window.location.href = 'index.php';
    }, 300000); // 300 seconds
    </script>
</body>
</html>


