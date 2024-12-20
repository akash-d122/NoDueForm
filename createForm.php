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

    // Step 1: Check if the form already exists
    $checkQuery = "SELECT form_id FROM no_due_forms WHERE department_id = ? AND year_semester = ? AND section = ? AND month = ? AND academic_year = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("issss", $department_id, $yearSemester, $section, $month, $academicYear);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Form already exists
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
        echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        icon: "warning",
                        title: "Form Already Exists",
                        text: "This No Due Form already exists. Please check in the No Due Form List.",
                        confirmButtonText: "Go to No Due Form List",
                        backdrop: true, // Make backdrop visible
                        background: "#fff", // Change background to white or any desired color
                        customClass: {
                            title: "custom-title",
                            content: "custom-content",
                            text: "custom-text",
                            confirmButton: "custom-button"
                        }                        
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "NoDueFormList.php";
                        }
                    });
                });
              </script>';
            echo '<style>
              .custom-title {
                  font-family: "Poppins", sans-serif;
                  font-size: 24px;
                  color: #29465B;
              }
              .custom-content {
                  font-family: "Poppins", sans-serif;
                  font-size: 16px;
                  color: #555;
              }
              .custom-text {
                  font-family: "Poppins", sans-serif;
                  font-size: 16px;
                  color: #357EC7;
              }
              .custom-button {
                  font-family: "Poppins", sans-serif;
                  font-size: 14px;
                  font-weight: bold;
                  color: white;
                  background-color: #007BFF;
                  border: none;
                  padding: 10px 20px;
                  border-radius: 5px;
                  cursor: pointer;
              }
              .custom-button:hover {
                  background-color: #0056b3;
              }
            </style>';
        exit;
    }
    $stmt->close();

    // Step 2: Insert the form data into the no_due_forms table
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
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style> 
        body {
            min-height: 100vh;
            background: linear-gradient(to bottom right, #c3e6cb, #ffffff);
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
                <a class="navbar-brand" href="#">Create of No Due Form Information</a>
            </div>
        </nav>

        <div class="container mt-5">
        <h2 style="color:#357EC7; font-weight: bold;" class="mb-4">Create a No Due Form</h2>
        <!-- <h2 class="mb-4">Create No Due Form</h2> -->
            <div class="card">
                <!-- <div class="card-header bg-dark bg-gradient text-light">
                    <b>No Due Form Details</b>
                </div> -->
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="department" class="form-label">Department</label>
                            <input type="text" style="width: 790px;" name="department" id="department" class="form-control" value="<?php echo htmlspecialchars($department_name); ?>" readonly>
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
                                    <option value="2024-25">2024-25</option>
                                    <option value="2025-26">2025-26</option>
                                    <option value="2026-27">2026-27</option>
                                    <option value="2027-28">2027-28</option>
                                    <option value="2028-29">2028-29</option>
                                    <option value="2029-30">2029-30</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-success">Click here to Create No Due Form</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div style="position:absolute; bottom:20px; width:82.2vw;">
            <footer class="footer mt-5 py-3 bg-light" style="border-radius:10px; background: linear-gradient(to left, #c3e1cb, #ffffff);" >
            <div class="text-center">
                <span style="font-size: 12px; color: #29465B;">
                    Developed & Hosted by <strong>MITS_InstituteDatabaseSystem@PAARC</strong>
                </span>
            </div>
            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    

</body>
</html>
