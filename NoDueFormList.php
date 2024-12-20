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

// Handle Edit Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_form'])) {
    $form_id = $_POST['form_id'];
    $year_semester = $_POST['year_semester'];
    $section = $_POST['section'];
    $month = $_POST['month'];
    $academic_year = $_POST['academic_year'];

    $query = "UPDATE no_due_forms SET year_semester = ?, section = ?, month = ?, academic_year = ? WHERE form_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ssssi', $year_semester, $section, $month, $academic_year, $form_id);

    if ($stmt->execute()) {
        // echo "<script>alert('Form updated successfully.'); window.location.href = 'NoDueFormList.php';</script>";
    } else {
        echo "<script>alert('Failed to update the form.');</script>";
    }
    $stmt->close();
}

// Handle Delete Request with foreign key constraints
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_form'])) {
    $form_id = $_POST['form_id'];

    // Start a MySQL transaction
    $conn->begin_transaction();

    try {
        $query = "DELETE FROM subject_faculty_mapping WHERE subject_id IN (SELECT subject_id FROM subjects WHERE form_id = ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $form_id);
        $stmt->execute();

        $query = "DELETE FROM student_subject_mapping WHERE subject_id IN (SELECT subject_id FROM subjects WHERE form_id = ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $form_id);
        $stmt->execute();

        $query = "DELETE FROM student_mentoring WHERE student_id IN (SELECT student_id FROM students WHERE form_id = ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $form_id);
        $stmt->execute();

        $query = "DELETE FROM student_assignments WHERE student_id IN (SELECT student_id FROM students WHERE form_id = ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $form_id);
        $stmt->execute();

        $query = "DELETE FROM students WHERE form_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $form_id);
        $stmt->execute();

        $query = "DELETE FROM mentoring_options WHERE form_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $form_id);
        $stmt->execute();

        $query = "DELETE FROM subjects WHERE form_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $form_id);
        $stmt->execute();

        $query = "DELETE FROM no_due_forms WHERE form_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $form_id);
        $stmt->execute();

        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>alert('Failed to delete the form: " . $e->getMessage() . "');</script>";
    }

    $stmt->close();
}

// Get the department_id of the logged-in user
$department_id = $_SESSION['department_id'];

// Pagination logic
$results_per_page = 10;
$query = "SELECT COUNT(*) FROM no_due_forms WHERE department_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $department_id);
$stmt->execute();
$stmt->bind_result($total_results);
$stmt->fetch();
$stmt->close();

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
            <table class="table table-bordered table-striped bg-dark " style="background-color: ;">
                <thead class="text-white">
                    <tr>
                        <th>S.No</th>
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
                    // Initialize serial number counter
                    $serialNumber = 1;

                    // Fetch department name for each form
                    while ($row = mysqli_fetch_assoc($result)) { 
                        // Fetch department name from department_id
                        $deptQuery = "SELECT department_name FROM departments WHERE department_id = '".$row['department_id']."'";
                        $deptResult = mysqli_query($conn, $deptQuery);
                        $deptRow = mysqli_fetch_assoc($deptResult);
                ?>
                    <tr>
                        <td><?php echo $serialNumber++; ?></td> <!-- Auto-incremented Serial Number -->
                        <td class="text-primary" style="cursor: pointer;" onclick="window.location.href='studentMap.php?form_id=<?php echo $row['form_id']; ?>'">
                            <?php echo $deptRow['department_name']; ?>
                        </td>
                        <td><?php echo $row['year_semester']; ?></td>
                        <td><?php echo $row['section']; ?></td>
                        <td><?php echo $row['month']; ?></td>
                        <td><?php echo $row['academic_year']; ?></td>
                        <td><?php echo $row['created_at']; ?></td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="form_id" value="<?php echo $row['form_id']; ?>">
                                <button type="button" class="btn btn-warning btn-sm edit-btn" data-bs-toggle="modal" data-bs-target="#editModal"
                                    data-form-id="<?php echo $row['form_id']; ?>"
                                    data-year-semester="<?php echo $row['year_semester']; ?>"
                                    data-section="<?php echo $row['section']; ?>"
                                    data-month="<?php echo $row['month']; ?>"
                                    data-academic-year="<?php echo $row['academic_year']; ?>">Edit</button>
                                <button type="button" class="btn btn-danger btn-sm delete-btn" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#deleteConfirmationModal" 
                                    data-form-id="<?php echo $row['form_id']; ?>">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>

            </table>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit No Due Form</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="editForm">
                        <input type="hidden" id="editFormId" name="form_id">
                        <input type="hidden" name="edit_form" value="1">
                        <div class="mb-3">
                            <label for="editYearSemester" class="form-label">Year & Semester</label>
                            <input type="text" class="form-control" id="editYearSemester" name="year_semester" required>
                        </div>
                        <div class="mb-3">
                            <label for="editSection" class="form-label">Section</label>
                            <input type="text" class="form-control" id="editSection" name="section" required>
                        </div>
                        <div class="mb-3">
                            <label for="editMonth" class="form-label">Month</label>
                            <input type="text" class="form-control" id="editMonth" name="month" required>
                        </div>
                        <div class="mb-3">
                            <label for="editAcademicYear" class="form-label">Academic Year</label>
                            <input type="text" class="form-control" id="editAcademicYear" name="academic_year" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- delete view -->
    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this form?</p>
                    <form method="POST" id="deleteForm">
                        <input type="hidden" name="form_id" id="deleteFormId">
                        <input type="hidden" name="delete_form" value="1">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script>
        // Populate modal with form data on Edit button click
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', () => {
                document.getElementById('editFormId').value = button.getAttribute('data-form-id');
                document.getElementById('editYearSemester').value = button.getAttribute('data-year-semester');
                document.getElementById('editSection').value = button.getAttribute('data-section');
                document.getElementById('editMonth').value = button.getAttribute('data-month');
                document.getElementById('editAcademicYear').value = button.getAttribute('data-academic-year');
            });
        });
    
        // Handle Delete button click
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', () => {
                const formId = button.getAttribute('data-form-id');
                document.getElementById('deleteFormId').value = formId;
            });
        });
    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>