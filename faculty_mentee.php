<?php
// Include database connection file
include('dbconn.php');

session_start();

// Check if the user is logged in
if (!isset($_SESSION['employee_id'])) {
    header('Location: index.php');
    exit;
}

// Get the logged-in faculty ID
$faculty_id = $_SESSION['employee_id'];

// Get the form ID from the URL
if (!isset($_GET['form_id']) || empty($_GET['form_id'])) {
    die('Form ID is required.');
}
$form_id = intval($_GET['form_id']);

// Handle form submission to update statuses
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['statuses'] as $student_id => $activities) {
        foreach ($activities as $activity => $status) {
            // Check if the status already exists
            $check_query = "
                SELECT COUNT(*) AS count 
                FROM student_mentoring 
                WHERE student_id = ? AND activity_type = ?";
            $check_stmt = $conn->prepare($check_query);
            $check_stmt->bind_param("is", $student_id, $activity);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            $exists = $check_result->fetch_assoc()['count'] > 0;

            if ($exists) {
                // Update the existing record
                $update_query = "
                    UPDATE student_mentoring 
                    SET completion_status = ? 
                    WHERE student_id = ? AND activity_type = ?";
                $update_stmt = $conn->prepare($update_query);
                $update_stmt->bind_param("sis", $status, $student_id, $activity);
                $update_stmt->execute();
            } else {
                // Insert a new record
                $insert_query = "
                    INSERT INTO student_mentoring (student_id, activity_type, completion_status) 
                    VALUES (?, ?, ?)";
                $insert_stmt = $conn->prepare($insert_query);
                $insert_stmt->bind_param("iss", $student_id, $activity, $status);
                $insert_stmt->execute();
            }
        }
    }
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

// Fetch all mentoring activities for the specific form
$query_activities = "
    SELECT option_name 
    FROM mentoring_options 
    WHERE form_id = ?";
$activities_stmt = $conn->prepare($query_activities);
$activities_stmt->bind_param("i", $form_id);
$activities_stmt->execute();
$activities_result = $activities_stmt->get_result();

$activity_columns = [];
while ($row = $activities_result->fetch_assoc()) {
    $activity_columns[] = $row['option_name'];
}

// Fetch students assigned to the logged-in mentor and mapped to the specific form
$query_students = "
    SELECT s.student_id, s.roll_number 
    FROM students s 
    WHERE s.mentor_id = ? AND s.form_id = ?";
$students_stmt = $conn->prepare($query_students);
$students_stmt->bind_param("si", $faculty_id, $form_id);
$students_stmt->execute();
$students_result = $students_stmt->get_result();
$students = $students_result->fetch_all(MYSQLI_ASSOC);

// Fetch activity statuses for each student
$student_activity_statuses = [];
foreach ($students as $student) {
    $student_id = $student['student_id'];
    $query_status = "
        SELECT mo.option_name, sm.completion_status
        FROM mentoring_options mo
        LEFT JOIN student_mentoring sm 
            ON mo.option_name = sm.activity_type AND sm.student_id = ?
        WHERE mo.form_id = ?";
    $status_stmt = $conn->prepare($query_status);
    $status_stmt->bind_param("ii", $student_id, $form_id);
    $status_stmt->execute();
    $status_result = $status_stmt->get_result();

    $statuses = [];
    while ($row = $status_result->fetch_assoc()) {
        $statuses[$row['option_name']] = $row['completion_status'] ?? '';
    }
    $student_activity_statuses[$student_id] = $statuses;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>MITS | NoDueForm</title>
    <link rel="stylesheet" href="stylesmentee.css">
    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
</head>
<body>
    <?php include('faculty_sidebar.php'); ?>
    <div class="content">
        <div class="image-container">
            <img src="Mits_logo_24-removebg-preview.png" alt="Table Image" height="200" width="800">
        </div>
        <div class="heading-section">
            <label>Mentoring & Other Information</label>
        </div>

        <form method="POST" action=""> 
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Roll Number</th>
                            <?php foreach ($activity_columns as $activity): ?>
                                <th><?php echo ucfirst(str_replace('_', ' ', $activity)); ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $index => $student): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo $student['roll_number']; ?></td>
                            <?php foreach ($activity_columns as $activity): ?>
                            <td>
                                <?php 
                                $status = $student_activity_statuses[$student['student_id']][$activity] ?? 'NA'; 
                                ?>
                                <input type="radio" name="statuses[<?php echo $student['student_id']; ?>][<?php echo $activity; ?>]" value="Yes" 
                                    <?php echo $status === 'Yes' ? 'checked' : ''; ?>> Yes<br>
                                <input type="radio" name="statuses[<?php echo $student['student_id']; ?>][<?php echo $activity; ?>]" value="NA" 
                                    <?php echo $status === 'NA' ? 'checked' : ''; ?>> N/A
                            </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="buttons">
            <button type="button" onclick="window.location.href='faculty_options.php?form_id=<?php echo $form_id; ?>'" class="add-btn">Home</button>                                
                <button type="submit">Save Changes</button>
            </div>
        </form>
    </div>
</body>
</html>
