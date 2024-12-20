<?php
session_start();
require_once('dbconn.php'); // Include the database config file here

// Check if user is logged in
if (!isset($_SESSION['department_id'])) {
    header('Location: login.php');
    exit;
}

$department_id = $_SESSION['department_id']; // Get the department ID from the session

// Fetch the department name using the department_id
$departmentQuery = "SELECT department_name FROM departments WHERE department_id = ?";
$stmt = $conn->prepare($departmentQuery);
$stmt->bind_param("i", $department_id);
$stmt->execute();
$stmt->bind_result($department_name);
$stmt->fetch();
$stmt->close();

if (!$department_name) {
    echo "Department information not found.";
    exit;
}

// Fetch the list of faculty for the department
$facultyQuery = "SELECT employee_id, name, email FROM faculty WHERE department_id = ?";
$facultyStmt = $conn->prepare($facultyQuery);
$facultyStmt->bind_param("i", $department_id);
$facultyStmt->execute();
$facultyResult = $facultyStmt->get_result();
$facultyStmt->close();

// Handle form submission for adding or editing faculty
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employeeId = $_POST['employeeId'];
    $facultyName = $_POST['facultyName'];
    $facultyEmail = $_POST['facultyEmail'];

    // Check if we are adding or updating a faculty member
    if (isset($_POST['action']) && $_POST['action'] == 'edit') {
        // Update existing faculty record
        $updateQuery = "UPDATE faculty SET name = ?, email = ? WHERE employee_id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("ssi", $facultyName, $facultyEmail, $employeeId);

        if ($stmt->execute()) {
            header('Location: addFaculty.php');
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        // Insert new faculty record
        $insertQuery = "INSERT INTO faculty (employee_id, name, email, department_id) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("sssi", $employeeId, $facultyName, $facultyEmail, $department_id);

        if ($stmt->execute()) {
            header('Location: addFaculty.php');
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <!-- Sidebar -->
    <?php include('sidebar.php'); ?>

    <!-- Content -->
    <div class="content">
        <div class="container mt-5">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3">Manage Faculty</h1>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFacultyModal">Add Faculty</button>
            </div>

            <!-- Faculty Table -->
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Employee ID</th>
                            <th>Faculty Name</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Ensure $facultyResult is set and contains data
                        if ($facultyResult->num_rows > 0) {
                            $index = 1;
                            while ($row = $facultyResult->fetch_assoc()) {
                                echo "
                                <tr>
                                    <td>{$index}</td>
                                    <td>{$row['employee_id']}</td>
                                    <td>{$row['name']}</td>
                                    <td>{$row['email']}</td>
                                    <td>
                                        <button class='btn btn-warning btn-sm' data-bs-toggle='modal' data-bs-target='#editFacultyModal' data-employee-id='{$row['employee_id']}' data-name='{$row['name']}' data-email='{$row['email']}'>Edit</button>
                                        <button class='btn btn-danger btn-sm'>Delete</button>
                                    </td>
                                </tr>
                                ";
                                $index++;
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center'>No faculty members found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Faculty Modal -->
    <div class="modal fade" id="addFacultyModal" tabindex="-1" aria-labelledby="addFacultyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addFacultyModalLabel">Add Faculty</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3">
                            <label for="employeeId" class="form-label">Employee ID</label>
                            <input type="text" name="employeeId" id="employeeId" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="facultyName" class="form-label">Faculty Name</label>
                            <input type="text" name="facultyName" id="facultyName" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="facultyEmail" class="form-label">Email</label>
                            <input type="email" name="facultyEmail" id="facultyEmail" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-success">Add Faculty</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Faculty Modal -->
    <div class="modal fade" id="editFacultyModal" tabindex="-1" aria-labelledby="editFacultyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editFacultyModalLabel">Edit Faculty</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="employeeId" id="editEmployeeId">
                        <div class="mb-3">
                            <label for="facultyName" class="form-label">Faculty Name</label>
                            <input type="text" name="facultyName" id="editFacultyName" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="facultyEmail" class="form-label">Email</label>
                            <input type="email" name="facultyEmail" id="editFacultyEmail" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-success">Update Faculty</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // When the Edit button is clicked, populate the modal fields with the faculty's current data
        const editButtons = document.querySelectorAll('.btn-warning');
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const employeeId = this.getAttribute('data-employee-id');
                const name = this.getAttribute('data-name');
                const email = this.getAttribute('data-email');

                document.getElementById('editEmployeeId').value = employeeId;
                document.getElementById('editFacultyName').value = name;
                document.getElementById('editFacultyEmail').value = email;
            });
        });
    </script>
</body>
</html>
