<?php
// Include the database connection and start the session
include 'dbconn.php';
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['employee_id'])) {
    // Redirect to login if user is not logged in
    header('Location: index.php');
    exit;
}

// Get the logged-in employee's name (Optional for greeting)
$employee_id = $_SESSION['employee_id'];
$query = "SELECT name FROM faculty WHERE employee_id = '$employee_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);
$faculty_name = $user['name'];

// Get the form_id from the URL
if (!isset($_GET['form_id'])) {
    die('Form ID is missing.');
}
$form_id = $_GET['form_id'];
// if (isset($_GET['form_id'])) {
//     $form_id = intval($_GET['form_id']); 
// } else {
//     echo "form_id parameter is missing.";
// }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Dashboard</title>
    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(to bottom right, #c3e1cb, #ffffff);
            background-size: cover;
            background-position: center;
            font-family: "Poppins", Georgia;
        }
        .rounded-navbar {
            border-radius: 15px;
        }
        .option-card {
            border: 1px solid #ddd;
            border-radius: 15px;
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .option-card:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <?php include('faculty_sidebar.php'); ?>

    <div class="content">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark rounded-navbar">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Subject or Mentor Information</a>
                <span class="navbar-text text-light">Welcome, <?php echo htmlspecialchars($faculty_name); ?></span>
            </div>
        </nav>

        <div class="container mt-5">
        <h2 style="color:#357EC7; font-weight: bold;" class="mb-4">Choose an Option</h2>
        <!-- <h2 class="mb-4">Choose an Option</h2> -->
            <div class="row g-4">
                <!-- Option 1: Subjects Related Students Data -->
                <div class="col-md-6">
                    <div class="option-card" onclick="window.location.href='faculty_subject.php?form_id=<?php echo $form_id; ?>'">
                        <h3 class="text-primary">Subjects Students Data to Approval</h3>
                        <p>View and manage student data for subjects you are assigned to.</p>
                    </div>
                </div>

                <!-- Option 2: Mentee's Data Form Approval -->
                <div class="col-md-6">
                    <div class="option-card" onclick="window.location.href='faculty_mentee.php?form_id=<?php echo $form_id; ?>'">
                        <h3 class="text-success">Mentee's Data Form Approval</h3>
                        <p>Review and approve no due forms submitted by your mentees.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Close the MySQLi connection (Optional here, as no heavy data processing)
mysqli_close($conn);
?>
