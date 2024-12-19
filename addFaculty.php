<?php
session_start();
require_once('dbconn.php'); // Include the database config file

// Check if user is logged in
if (!isset($_SESSION['department_id'])) {
    header('Location: login.php');
    exit;
}

$department_id = $_SESSION['department_id'];

// Handle adding a new faculty member
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $employeeId = $_POST['employeeId'];
    $facultyName = $_POST['facultyName'];
    $facultyEmail = $_POST['facultyEmail'];
    $facultyPassword = $_POST['facultyPassword'];

    // Check for duplicate employee ID
    $checkQuery = "SELECT employee_id FROM faculty WHERE employee_id = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("s", $employeeId);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Employee ID already exists.']);
    } else {
        $hashedPassword = password_hash($facultyPassword, PASSWORD_DEFAULT);

        $insertQuery = "INSERT INTO faculty (employee_id, name, email, password, department_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("ssssi", $employeeId, $facultyName, $facultyEmail, $hashedPassword, $department_id);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'email' => $facultyEmail, 'name' => $facultyName, 'password' => $facultyPassword]);
        } else {
            echo json_encode(['status' => 'error', 'message' => $stmt->error]);
        }
        $stmt->close();
    }
    $checkStmt->close();
    exit;
}

// Handle editing a faculty member
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $employeeId = $_POST['employeeId'];
    $facultyName = $_POST['facultyName'];
    $facultyEmail = $_POST['facultyEmail'];

    $updateQuery = "UPDATE faculty SET name = ?, email = ? WHERE employee_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sss", $facultyName, $facultyEmail, $employeeId);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Faculty updated successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $stmt->error]);
    }
    $stmt->close();
    exit;
}

// Handle deleting a faculty member
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $employeeId = $_POST['employeeId'];

    // Step 1: Delete records from subject_faculty_mapping
    $deleteMappingQuery = "DELETE FROM subject_faculty_mapping WHERE employee_id = ?";
    $stmt1 = $conn->prepare($deleteMappingQuery);
    $stmt1->bind_param("s", $employeeId);
    $stmt1->execute();

    // Step 2: Delete records from student_subject_mapping
    $deleteStudentMappingQuery = "DELETE FROM student_subject_mapping WHERE employee_id = ?";
    $stmt2 = $conn->prepare($deleteStudentMappingQuery);
    $stmt2->bind_param("s", $employeeId);
    $stmt2->execute();

    // Step 3: Delete records from student_mentoring
    $deleteMentoringQuery = "DELETE FROM student_mentoring WHERE student_id IN (SELECT student_id FROM students WHERE mentor_id = ?)";
    $stmt3 = $conn->prepare($deleteMentoringQuery);
    $stmt3->bind_param("s", $employeeId);
    $stmt3->execute();

    // Step 4: Delete the faculty record
    $deleteFacultyQuery = "DELETE FROM faculty WHERE employee_id = ?";
    $stmt4 = $conn->prepare($deleteFacultyQuery);
    $stmt4->bind_param("s", $employeeId);

    if ($stmt4->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Faculty deleted successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $stmt4->error]);
    }

    $stmt1->close();
    $stmt2->close();
    $stmt3->close();
    $stmt4->close();
    exit;
}

// Fetch the department name
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Management</title>
    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        .body {
            background: linear-gradient(to bottom right, #d8f0dc, #ffffff);
        }
    </style>
</head>
<body style="background: linear-gradient(to bottom right, #d8f0dc, #ffffff); background-repeat: no-repeat; background-attachment: fixed; background-size: cover;">
    <!-- Sidebar -->
    <?php include('sidebar.php'); ?>

    <!-- Content -->
    <div class="content">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark rounded-navbar">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Add Faculty Members Information</a>
            </div>
        </nav>
        <div class="container mt-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 style="color:#357EC7; font-weight: bold;" class="mb-4">Manage Faculty</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFacultyModal">Add Faculty</button>
            </div>

            <!-- Faculty Table -->
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Sl.No</th>
                            <th>Employee ID</th>
                            <th>Faculty Name</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
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
                                        <button class='btn btn-danger btn-sm' data-bs-toggle='modal' data-bs-target='#deleteFacultyModal' data-employee-id='{$row['employee_id']}'>Delete</button>
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

    <div class="modal fade" id="addFacultyModal" tabindex="-1" aria-labelledby="addFacultyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addFacultyModalLabel">Add Faculty</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addFacultyForm">
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
                        <div class="mb-3">
                            <label for="facultyPassword" class="form-label">Password</label>
                            <input type="password" name="facultyPassword" id="facultyPassword" class="form-control" required>
                        </div>
                        <input type="hidden" name="action" value="add">
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
                    <form id="editFacultyForm">
                        <div class="mb-3">
                            <label for="editEmployeeId" class="form-label">Employee ID</label>
                            <input type="text" name="employeeId" id="editEmployeeId" class="form-control" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="editFacultyName" class="form-label">Faculty Name</label>
                            <input type="text" name="facultyName" id="editFacultyName" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="editFacultyEmail" class="form-label">Email</label>
                            <input type="email" name="facultyEmail" id="editFacultyEmail" class="form-control" required>
                        </div>
                        <input type="hidden" name="action" value="edit">
                        <button type="submit" class="btn btn-warning">Update Faculty</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Faculty Modal -->
    <div class="modal fade" id="deleteFacultyModal" tabindex="-1" aria-labelledby="deleteFacultyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteFacultyModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this faculty member?</p>
                    <form id="deleteFacultyForm">
                        <input type="hidden" name="employeeId" id="deleteEmployeeId">
                        <input type="hidden" name="action" value="delete">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>

document.querySelector("#addFacultyForm").addEventListener("submit", function (e) {
    e.preventDefault();

    const formData = new FormData(this);

    // Send faculty data to the server
    fetch("", { // Same PHP script handles adding faculty
        method: "POST",
        body: formData,
    })
        .then((response) => response.json())
        .then((data) => {
    console.log("Response from server:", data); // Debug server response
    if (data.status === "success") {
        console.log("Preparing email data:", {
            email: data.email,
            name: data.name,
            password: data.password,
        }); // Log email data
        alert("Faculty added successfully!");

        // Send email asynchronously
        fetch("sendEmail.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                email: data.email,
                name: data.name,
                password: data.password,
            }),
        })
            .then((emailResponse) => {
                console.log("Email response object:", emailResponse); // Debug raw response
                return emailResponse.json();
            })
            .then((emailData) => {
                console.log("Email data received from sendEmail.php:", emailData);
                if (emailData.status === "success") {
                    alert("Email sent successfully!");
                } else {
                    console.error("Email Error:", emailData.message);
                }
            })
            .catch((error) => console.error("Error sending email:", error));
    } else {
        alert("Error: " + data.message);
    }
})
        .catch((error) => console.error("Error:", error));
});

        // Set data in edit modal
        document.querySelectorAll("[data-bs-target='#editFacultyModal']").forEach(button => {
            button.addEventListener("click", function () {
                const employeeId = this.getAttribute("data-employee-id");
                const name = this.getAttribute("data-name");
                const email = this.getAttribute("data-email");

                document.querySelector("#editEmployeeId").value = employeeId;
                document.querySelector("#editFacultyName").value = name;
                document.querySelector("#editFacultyEmail").value = email;
            });
        });

        // Set data in delete modal
        document.querySelectorAll("[data-bs-target='#deleteFacultyModal']").forEach(button => {
            button.addEventListener("click", function () {
                const employeeId = this.getAttribute("data-employee-id");
                document.querySelector("#deleteEmployeeId").value = employeeId;
            });
        });

        // Edit faculty form submission
        document.querySelector("#editFacultyForm").addEventListener("submit", function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch("", { method: "POST", body: formData })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") {
                        // alert(data.message);
                        window.location.reload();
                    } else {
                        alert("Error: " + data.message);
                    }
                });
        });

        // Delete faculty form submission
        document.querySelector("#deleteFacultyForm").addEventListener("submit", function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch("", { method: "POST", body: formData })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") {
                        
                        window.location.reload();
                    } else {
                        alert("Error: " + data.message);
                    }
                });
        });
    </script>
</body>
</html>

