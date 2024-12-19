<?php
// Include database connection and session start
include 'dbconn.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['employee_id'])) {
    header('Location: index.php');
    exit;
}

// Get the logged-in faculty ID
$faculty_id = $_SESSION['employee_id'];

// Check if form_id is passed
if (!isset($_GET['form_id'])) {
    die('Form ID is missing.');
}

// Retrieve the form_id from the URL
$form_id = $_GET['form_id'];

// Handle Save request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    foreach ($_POST['students'] as $student) {
        $student_id = $student['student_id'];
        $subject_id = $student['subject_id'];
        $assignment_1_status = isset($student['assignment_1_status']) ? 1 : 0;
        $assignment_2_status = isset($student['assignment_2_status']) ? 1 : 0;
        $remarks = $student['remarks'];

        // Update student assignments in the database
        $query = "
            UPDATE student_assignments
            SET 
                assignment_1_status = ?, 
                assignment_2_status = ?, 
                remarks = ?
            WHERE 
                student_id = ? AND 
                subject_id = ?
        ";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iisii", $assignment_1_status, $assignment_2_status, $remarks, $student_id, $subject_id);
        $stmt->execute();
    }

    // Include SweetAlert script only after saving data
    echo "
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Data Saved Successfully!',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.href = 'faculty_options.php?form_id=$form_id';
            });
        });
    </script>
    ";
    exit;
}

// Fetch subject-wise student data based on faculty and form
$query = "
    SELECT 
        s.student_id,
        s.roll_number,
        a.assignment_1_status,
        a.assignment_2_status,
        a.remarks,
        sub.subject_name,
        sub.subject_id
    FROM 
        student_subject_mapping sm
    JOIN 
        students s ON sm.student_id = s.student_id
    JOIN 
        subjects sub ON sm.subject_id = sub.subject_id
    LEFT JOIN 
        student_assignments a ON s.student_id = a.student_id AND a.subject_id = sub.subject_id
    WHERE 
        sm.employee_id = ? AND 
        sub.form_id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $faculty_id, $form_id);
$stmt->execute();
$result = $stmt->get_result();

// Prepare data for display
$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

$stmt->close();
?>


<!DOCTYPE html>
<html>
<head>
    <title>Home | Resources Information</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: Poppins, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            background: linear-gradient(to bottom right, #d8f0dc, #ffffff);
        }
        .content {
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
        }
        .heading-section label {
            font-family: Poppins;
            color: #357EC7;
            font-weight: bold;
            font-size: 32px;
            margin-bottom: 20px;
        }
        .form-row {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            gap: 20px;
            margin-top: 20px;
        }
        .form-row label {
            min-width: 150px;
        }
        .form-row select, 
        .form-row input {
            padding: 8px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
            margin-left: 4vw;
        }
        .buttons button {
            width: 120px;
            height: 40px;
            border: none;
            border-radius: 5px;
            background-color: #0056b3;
            color: #fff;
            cursor: pointer;
            font-size: 14px;
            font-family: Poppins;
        }
        .buttons button:hover {
            background-color: green;
        }

        .table-container {
            margin: 20px auto;
            width: 80%;
            font-family: Poppins, Arial, sans-serif;
        }
        table {
            width: 110%;
            border-collapse: collapse;
            text-align: left;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
        }
        th {
            background-color: #f4f4f4;
        }
        input[type="text"] {
            border: none;
            background-color: transparent;
            outline: none;
            padding: 5px;
        }
        input[type="text"]:focus {
            border-bottom: 1px solid #ccc;
            background-color: transparent;
        }
    </style>
</head>
<body>

<?php include('faculty_sidebar.php'); ?>

<div class="content">
    <div style="text-align: center;">
        <img src="Mits_logo_24-removebg-preview.png" alt="Table Image" height="200" width="800">
    </div>
    <div class="heading-section">
        <label><center>Subject wise Faculty Approval Information</center></label>
    </div>
    <form method="POST">
        <label style="width: 10%; font-family: Poppins; color: purple; font-size: 20px; margin-left: 175px;">Students List of Subject</label>
        
        <div class="table-container">
            <table border="1" style="width: 110%; border-collapse: collapse; font-family: Poppins;">
                <thead style="height:50px">
                    <tr>
                        <th style="color: purple; width: 10px;">S.No</th>
                        <th style="color: purple; width: 150px;">Roll Number</th>
                        <th style="color: purple; width: 200px;">Subject</th>
                        <th style="text-align: center; color: purple; width: 100px;">Assignment-1
                            <input type="checkbox" id="selectAllSubject1" style="margin-left: 5px;" onclick="toggleCheckboxes('subject1')">
                        </th>
                        <th style="text-align: center; color: purple; width: 100px;">Assignment-2
                            <input type="checkbox" id="selectAllSubject2" style="margin-left: 5px;" onclick="toggleCheckboxes('subject2')">
                        </th>
                        <th style="color: purple; width: 250px;">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($students) > 0): ?>
                        <?php foreach ($students as $index => $student): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo htmlspecialchars($student['roll_number']); ?></td>
                                <td><?php echo htmlspecialchars($student['subject_name']); ?></td>
                                <td style="text-align: center; vertical-align: middle;">
                                    <input type="checkbox" name="students[<?php echo $index; ?>][assignment_1_status]" class="subject1" style="width: 20px; height: 20px;" <?php echo $student['assignment_1_status'] ? 'checked' : ''; ?>>
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    <input type="checkbox" name="students[<?php echo $index; ?>][assignment_2_status]" class="subject2" style="width: 20px; height: 20px;" <?php echo $student['assignment_2_status'] ? 'checked' : ''; ?>>
                                </td>
                                <td>
                                    <input type="text" name="students[<?php echo $index; ?>][remarks]" value="<?php echo htmlspecialchars($student['remarks']); ?>" placeholder="Remarks" style="width: 100%; height: 30px; font-family: Poppins;">
                                </td>
                                <input type="hidden" name="students[<?php echo $index; ?>][student_id]" value="<?php echo $student['student_id']; ?>">
                                <input type="hidden" name="students[<?php echo $index; ?>][subject_id]" value="<?php echo $student['subject_id']; ?>">
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">No students found for this subject.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="buttons">
        <button type="button" onclick="window.location.href='faculty_options.php?form_id=<?php echo $form_id; ?>'" class="add-btn">Home</button>                                
           
        <!-- <button type="button" onclick="window.location.href='faculty_options.php?form_id=0'" class="add-btn">Home</button> -->
            <button type="submit" name="save" >Save</button>
        </div>
    </form>
</div>

<script>
    function toggleCheckboxes(className) {
        const checkboxes = document.querySelectorAll(`.${className}`);
        const selectAll = document.getElementById(`selectAll${className === 'subject1' ? 'Subject1' : 'Subject2'}`);
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
    }
</script>

</body>
</html>
