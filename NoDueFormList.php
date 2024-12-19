<?php
// Include the database connection
include 'dbconn.php';

// Start the session to track logged-in user
session_start();

// Assuming you have a session variable for the logged-in user's department_id
if (!isset($_SESSION['department_id'])) {
    // Redirect to login if user is not logged in
    header('Location: login.php');
    exit;
}

// Get the department_id of the logged-in user
$department_id = $_SESSION['department_id'];

// Pagination logic
$results_per_page = 10;
$query = "SELECT COUNT(*) FROM no_due_forms WHERE department_id = '$department_id'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_row($result);
$total_results = $row[0];
$total_pages = ceil($total_results / $results_per_page);
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start_from = ($page - 1) * $results_per_page;

// Query to fetch the No Due Forms for the logged-in department
$query = "SELECT * FROM no_due_forms WHERE department_id = '$department_id' LIMIT $start_from, $results_per_page";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Created No Due Forms</title>
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
            border-radius: 15px; /* Adjust the radius as needed */
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <?php include('sidebar.php'); ?>

    <div class="content">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark rounded-navbar">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">No Due Form List Information</a>
            </div>
        </nav>
        <div class="container mt-5">
            <h2 style="color:#357EC7; font-weight: bold;" class="mb-4">List of No Due Forms</h2>
            <table class="table table-bordered table-striped" style="border: 1px black solid">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Department</th>
                        <th>Year & Semester</th>
                        <th>Section</th>
                        <th>Month</th>
                        <th>Academic Year</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody style="background-color: white;">
                    <?php 
                    // Fetch department name for each form
                    while ($row = mysqli_fetch_assoc($result)) { 
                        // Fetch department name from department_id
                        $deptQuery = "SELECT department_name FROM departments WHERE department_id = '".$row['department_id']."'";
                        $deptResult = mysqli_query($conn, $deptQuery);
                        $deptRow = mysqli_fetch_assoc($deptResult);
                    ?>
                        <tr onclick="window.location.href='studentMap.php?form_id=<?php echo $row['form_id']; ?>'" style="cursor: pointer;">
                            <td><?php echo $row['form_id']; ?></td>
                            <td class="text-primary"><?php echo $deptRow['department_name']; ?></td>
                            <td><?php echo $row['year_semester']; ?></td>
                            <td><?php echo $row['section']; ?></td>
                            <td><?php echo $row['month']; ?></td>
                            <td><?php echo $row['academic_year']; ?></td>
                            <td><?php echo $row['created_at']; ?></td>
                            <td>
                                        <button class='btn btn-warning btn-sm' data-bs-toggle='modal' data-bs-target='#editFacultyModal' data-employee-id='{$row['employee_id']}' data-name='{$row['name']}' data-email='{$row['email']}'>Edit</button>
                                        <button class='btn btn-danger btn-sm'>Delete</button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <!-- <nav>
                <ul class="pagination">
                    <li class="page-item <?php if ($page == 1) echo 'disabled'; ?>">
                        <a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a>
                    </li>
                    <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                        <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php } ?>
                    <li class="page-item <?php if ($page == $total_pages) echo 'disabled'; ?>">
                        <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
                    </li>
                </ul>
            </nav> -->
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Close the MySQLi connection
mysqli_close($conn);
?>
