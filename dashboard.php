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
        background-repeat: no-repeat; /* Prevents repetition of the gradient */
        background-attachment: fixed; /* Makes the gradient fixed while scrolling */
        background-size: cover; /* Ensures the gradient covers the entire screen */
        
        font-family: Poppins, sans-serif;
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
        color: black; /* Slightly darker green for headings */
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
        color: #357EC7; /* Bold text in dark green */
        font-family: Poppins;
        font-size: 36;
        font-weight: bold;
    }


    </style>
</head>
<body>
    <!-- Sidebar -->
    <?php include('sidebar.php'); ?>

    <!-- Main content -->
     <div class="ex">
        <img src="Mits_logo_24-removebg-preview.png" alt="MITS Logo" height="200" width="800">
    </div>
    <meta http-equiv="refresh" content="300; url=index.php">    
    <div class="header-board">
        <h1 style="color:#357EC7; font-family: Poppins;" class="text-center"><strong><?php echo $department; ?> </strong>Department Dashboard</h1>
        <p style="color:#357EC7; font-family: Poppins;" class="text-center">Logged in Department as: <strong><?php echo $department; ?></strong></p>
    </div>
    <div style="position:absolute; bottom:20px; width:82.2vw;margin-left:16%;">
        <footer class="footer mt-5 py-3 bg-light" style="border-radius:10px; background: linear-gradient(to left, #c3e1cb, #ffffff);" >
        <div class="text-center">
            <span style="font-size: 12px; color: #29465B;">
                Developed & Hosted by <strong>MITS_InstituteDatabaseSystem@PAARC</strong>
            </span>
        </div>
        </footer>
    </div>
    

    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script>
setTimeout(() => {
        // Redirect to index.php
        window.location.href = 'index.php';
    }, 3000000); // 300 seconds
</script>
</body>
</html>



