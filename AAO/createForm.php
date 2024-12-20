<?php
session_start();
require_once('dbconn.php'); // Include the database config file here

// Check if user is logged in
if (!isset($_SESSION['department_id'])) {
    header('Location: login.php');
    exit;
}

$department_id = $_SESSION['department_id'];

// Fetch the department name using the department_id
$departmentQuery = "SELECT department_name FROM departments WHERE department_id = ?";
$stmt = $conn->prepare($departmentQuery);
$stmt->bind_param("i", $department_id); // Bind the department_id to the query
$stmt->execute();
$stmt->bind_result($department_name);
$stmt->fetch();
$stmt->close();

if (!$department_name) {
    echo "Department information not found.";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $yearSemester = $_POST['yearSemester'];
    $section = $_POST['section'];
    $month = $_POST['month'];
    $academicYear = $_POST['academicYear'];

    // Step 1: Insert the form data into the no_due_forms table
    $insertQuery = "INSERT INTO no_due_forms (department_id, year_semester, section, month, academic_year, created_at) 
                    VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("issss", $department_id, $yearSemester, $section, $month, $academicYear);
    
    // Execute the query
    if ($stmt->execute()) {
        // Redirect to the list page after successful insertion
        header('Location: NoDueFormList.php');
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}

// Close the MySQLi connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>No Due Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <!-- Sidebar -->
    <?php include('sidebar.php'); ?>

    <div class="content">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">No Due Form</a>
            </div>
        </nav>

        <div class="container mt-5">
            <h2 class="mb-4">Create No Due Form</h2>
            <div class="card">
                <div class="card-header bg-primary text-white">
                    No Due Form Details
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="department" class="form-label">Department</label>
                            <input type="text" name="department" id="department" class="form-control" value="<?php echo htmlspecialchars($department_name); ?>" readonly>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="yearSemester" class="form-label">Year & Semester</label>
                                <select name="yearSemester" id="yearSemester" class="form-select" required>
                                    <option value="">-- Select Year here --</option>
                                    <option value="I - I">First Year I Semester</option>
                                    <option value="I - II">First Year II Semester</option>
                                    <option value="II - I">Second Year I Semester</option>
                                    <option value="II - II">Second Year II Semester</option>
                                    <option value="III - I">Third Year I Semester</option>
                                    <option value="III - II">Third Year II Semester</option>
                                    <option value="IV - I">Final Year I Semester</option>
                                    <option value="IV - II">Final Year II Semester</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="section" class="form-label">Section</label>
                                <select name="section" id="section" class="form-select" required>
                                    <option value="">-- Select Section here --</option>
                                    <option value="A">A Section</option>
                                    <option value="B">B Section</option>
                                    <option value="C">C Section</option>
                                    <option value="D">D Section</option>
                                    <option value="E">E Section</option>
                                    <option value="F">F Section</option>
                                    <option value="G">G Section</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mt-3">
                            <div class="col-md-4">
                                <label for="month" class="form-label">Month</label>
                                <select name="month" id="month" class="form-select" required>
                                    <option value="">-- Select Month here --</option>
                                    <option value="January">January</option>
                                    <option value="February">February</option>
                                    <option value="March">March</option>
                                    <option value="April">April</option>
                                    <option value="May">May</option>
                                    <option value="June">June</option>
                                    <option value="July">July</option>
                                    <option value="August">August</option>
                                    <option value="September">September</option>
                                    <option value="October">October</option>
                                    <option value="November">November</option>
                                    <option value="December">December</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="academicYear" class="form-label">Academic Year</label>
                                <select name="academicYear" id="academicYear" class="form-select" required>
                                    <option value="">-- Select Year here --</option>
                                    <option value="2024">2024</option>
                                    <option value="2025">2025</option>
                                    <option value="2026">2026</option>
                                    <option value="2027">2027</option>
                                    <option value="2028">2028</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-success">Create No Due Form</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
