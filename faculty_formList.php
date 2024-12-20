<?php
// Include the database connection
include 'dbconn.php';

// Start the session to track logged-in user
session_start();

// Assuming you have a session variable for the logged-in user's employee_id
if (!isset($_SESSION['employee_id'])) {
    // Redirect to login if user is not logged in
    header('Location: index.php');
    exit;
}

// Get the employee_id of the logged-in user
$employee_id = $_SESSION['employee_id'];

// Pagination logic
$results_per_page = 10;

// Query to count the total results considering both mentor and subject mapping
$count_query = "
    SELECT COUNT(DISTINCT nd.form_id) 
    FROM no_due_forms nd
    LEFT JOIN students s ON nd.form_id = s.form_id
    LEFT JOIN student_subject_mapping sm ON s.student_id = sm.student_id
    WHERE s.mentor_id = '$employee_id' 
       OR sm.employee_id = '$employee_id'
";
$count_result = mysqli_query($conn, $count_query);
$count_row = mysqli_fetch_row($count_result);
$total_results = $count_row[0];
$total_pages = ceil($total_results / $results_per_page);
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start_from = ($page - 1) * $results_per_page;

// Query to fetch the No Due Forms based on mentor and subject mapping
$data_query = "
    SELECT DISTINCT nd.*, d.department_name
    FROM no_due_forms nd
    LEFT JOIN students s ON nd.form_id = s.form_id
    LEFT JOIN student_subject_mapping sm ON s.student_id = sm.student_id
    LEFT JOIN departments d ON nd.department_id = d.department_id
    WHERE s.mentor_id = '$employee_id' 
       OR sm.employee_id = '$employee_id'
    LIMIT $start_from, $results_per_page
";
$data_result = mysqli_query($conn, $data_query);

// Fetch the results into an array
$forms = [];
while ($row = mysqli_fetch_assoc($data_result)) {
    $forms[] = $row;
}

// Close the database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty No Due Forms</title>
    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(to bottom right, #c3e1cb, #ffffff);
            font-family: "Poppins", Georgia;
        }
        .rounded-navbar {
            border-radius: 15px;
        }
        tbody tr:hover {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <?php include('faculty_sidebar.php'); ?>

    <div class="content">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark rounded-navbar">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Faculty Dashboard</a>
            </div>
        </nav>

        <div class="container mt-5">
            <!-- <h1 style="color:#357EC7; font-family: Poppins;" class="text-center">Welcome to the Dashboard</h1> -->
            <h2 style="color:#357EC7; font-weight: bold;" class="mb-4">Faculty Mapped No Due Form List</h2>
            <table class="table table-bordered table-striped" style="border: 1px solid black;">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Department</th>
                        <th>Year & Semester</th>
                        <th>Section</th>
                        <th>Month</th>
                        <th>Academic Year</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody style="background-color: white;">
                    <?php if (count($forms) > 0): ?>
                        <?php foreach ($forms as $index => $form): ?>
                            <tr onclick="window.location.href='faculty_options.php?form_id=<?php echo $form['form_id']; ?>'" style="cursor: pointer;">
                                <td><?php echo $start_from + $index + 1; ?></td>
                                <td class="text-primary"><?php echo htmlspecialchars($form['department_name']); ?></td>
                                <td><?php echo htmlspecialchars($form['year_semester']); ?></td>
                                <td><?php echo htmlspecialchars($form['section']); ?></td>
                                <td><?php echo htmlspecialchars($form['month']); ?></td>
                                <td><?php echo htmlspecialchars($form['academic_year']); ?></td>
                                <td><?php echo htmlspecialchars($form['created_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No forms found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <nav>
                <ul class="pagination">
                    <li class="page-item <?php if ($page == 1) echo 'disabled'; ?>">
                        <a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a>
                    </li>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?php if ($page == $total_pages) echo 'disabled'; ?>">
                        <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
