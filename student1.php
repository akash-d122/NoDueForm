<?php
// Include the database connection file
include('dbconn.php');

// Check if 'name' is passed in the query string
if (isset($_GET['roll_number'])) {
    $roll_number = $_GET['roll_number'];

    // Fetch student details
    $query_student = "
        SELECT s.name, s.roll_number, ndf.year_semester, ndf.section
        FROM students s
        JOIN no_due_forms ndf ON s.form_id = ndf.form_id
        WHERE s.roll_number = ?";
    $stmt_student = $conn->prepare($query_student);
    $stmt_student->bind_param("s", $roll_number);
    $stmt_student->execute();
    $result_student = $stmt_student->get_result();
    $student_data = $result_student->fetch_assoc();

    if ($student_data) {
        // Fetch subject and assignment data
        $query_subjects = "
            SELECT sub.subject_name, f.name AS faculty_name, 
                   sa.assignment_1_status, sa.assignment_2_status, sa.remarks
            FROM student_subject_mapping ssm
            JOIN subjects sub ON ssm.subject_id = sub.subject_id
            JOIN faculty f ON ssm.employee_id = f.employee_id
            LEFT JOIN student_assignments sa 
                   ON ssm.student_id = sa.student_id AND sa.subject_id = ssm.subject_id
            WHERE ssm.student_id = (SELECT student_id FROM students WHERE roll_number = ?)";
        $stmt_subjects = $conn->prepare($query_subjects);
        $stmt_subjects->bind_param("s", $roll_number);
        $stmt_subjects->execute();
        $result_subjects = $stmt_subjects->get_result();
        $subjects_data = $result_subjects->fetch_all(MYSQLI_ASSOC);

        // Fetch mentoring activity data
        $query_mentoring = "
            SELECT mo.option_name, sm.completion_status
            FROM mentoring_options mo
            LEFT JOIN student_mentoring sm 
                   ON mo.option_name = sm.activity_type 
                   AND sm.student_id = (SELECT student_id FROM students WHERE roll_number = ?)
            WHERE mo.form_id = (SELECT form_id FROM students WHERE roll_number = ?)";
        $stmt_mentoring = $conn->prepare($query_mentoring);
        $stmt_mentoring->bind_param("ss", $roll_number, $roll_number);
        $stmt_mentoring->execute();
        $result_mentoring = $stmt_mentoring->get_result();
        $mentoring_data = $result_mentoring->fetch_all(MYSQLI_ASSOC);
    } else {
        $student_data = null;
        $subjects_data = [];
        $mentoring_data = [];
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Home | Student Information</title>
    <link rel="stylesheet" type="text/css" href="stylesStudent.css">
    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Sidebar -->
    <?php include('sidebar.php'); ?>

    <div class="content" style="margin-left:22%;">
        <div style="text-align: center; margin-bottom: 20px;">
            <img src="Mits_logo_24-removebg-preview.png" alt="Logo" height="200" width="800">
        </div>
        <div class="heading-section">
            <h2 style="font-weight: bold; font-size: 28px; text-align: center; color: #357EC7;">Status of Student No Due Form</h2>
        </div>

        <div style="margin-left: 2vw;">
            <?php if (!empty($student_data)): ?>
            <!-- Student Details -->
            <div class="form-row">
                <div>
                    <label style="color: #357EC7;">Student Name:</label>
                    <input type="text" style="width: 175px; margin-left: 20px; border: none; font-weight: bold; color: black;" 
                           value="<?php echo htmlspecialchars($student_data['name']); ?>" readonly>
                </div>
                <div>
                    <label style="color: #357EC7;">Roll Number:</label>
                    <input type="text" style="width: 175px; border: none; font-weight: bold; color: black;" 
                           value="<?php echo htmlspecialchars($student_data['roll_number']); ?>" readonly>
                </div>
            </div>
            <div class="form-row">
                <div>
                    <label style="color: #357EC7;">Year & Semester:</label>
                    <input type="text" style="width: 175px; border: none; font-weight: bold; color: black;" 
                           value="<?php echo htmlspecialchars($student_data['year_semester'] ?? 'N/A'); ?>" readonly>
                </div>
                <div>
                    <label style="color: #357EC7;">Section:</label>
                    <input type="text" style="width: 175px; margin-left: 40px; border: none; font-weight: bold; color: black;" 
                           value="<?php echo htmlspecialchars($student_data['section'] ?? 'N/A'); ?>" readonly>
                </div>
            </div>

            <!-- Subject Details -->
            <label class="subject-info-label">Subject to Faculty Information</label>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>S.No.</th>
                            <th>Name of the Subject</th>
                            <th>Faculty Name</th>
                            <th>Assignment-1</th>
                            <th>Assignment-2</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($subjects_data as $index => $subject): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($subject['subject_name']); ?></td>
                            <td><?php echo htmlspecialchars($subject['faculty_name']); ?></td>
                            <td style="text-align: center;">
                                <?php echo intval($subject['assignment_1_status']) == 1 ? 
                                    '<i class="fa-regular fa-circle-check fa-xl" style="color: #63E6BE;"></i><br>Approved' : 
                                    '<i class="fa-solid fa-spinner fa-rotate-90 fa-xl" style="color: #25378d;"></i><br>Pending'; ?>
                            </td>
                            <td style="text-align: center;">
                                <?php echo intval($subject['assignment_2_status']) == 1 ? 
                                    '<i class="fa-regular fa-circle-check fa-xl" style="color: #63E6BE;"></i><br>Approved' : 
                                    '<i class="fa-solid fa-spinner fa-rotate-90 fa-xl" style="color: #25378d;"></i><br>Pending'; ?>
                            </td>
                            <td><?php echo htmlspecialchars($subject['remarks'] ?? ''); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Mentoring Details -->
            <label class="subject-info-label">Mentoring & Other Information</label>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>S.No.</th>
                            <th>Activity</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($mentoring_data as $index => $mentoring): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($mentoring['option_name']); ?></td>
                            <td style="text-align: center;">
                                <?php echo $mentoring['completion_status'] === "Yes" ? 
                                    '<i class="fa-regular fa-circle-check fa-xl" style="color: #63E6BE;"></i><br>Approved' : 
                                    ($mentoring['completion_status'] === "NA" ? 
                                    '<i class="fa-solid fa-circle-question fa-xl" style="color: #FFA500;"></i><br>Not Applicable' : 
                                    '<i class="fa-solid fa-spinner fa-rotate-90 fa-xl" style="color: #25378d;"></i><br>Pending'); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="footer-row" style="font-size: 14px; text-align: center; height: 24px; margin-top: 30px; background-color: #f1f1f1; color: #333; padding: 5px; margin-right: 2vw;">
                Note: The above information is provisional and provided for reference purposes only.
            </div>
            <div style="text-align: center; margin-top: 20px;">
                <button onclick="goBack()" style="padding: 10px 20px; font-size: 16px; background-color: #357EC7; color: white; border: none; border-radius: 5px; cursor: pointer;">
                    Back
                </button>
            </div>
            <?php else: ?>
                <div style="color: red; font-weight: bold; text-align: center;">Student not found. Please check the roll number.</div>
            <?php endif; ?>
        </div>
        
    </div>
    
    
    
    <script>
        function goBack() {
            window.history.back();
        }
    </script>

</body>
</html>
