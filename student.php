<?php
// Include the database connection file
include('dbconn.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['roll_number'])) {
    $roll_number = $_POST['roll_number'];

    // Fetch student details
    $query_student = "
        SELECT s.roll_number, s.name, ndf.year_semester, ndf.section
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
            SELECT sub.subject_name, f.name AS faculty_name, sa.assignment_1_status, sa.assignment_2_status, sa.remarks
            FROM student_subject_mapping ssm
            JOIN subjects sub ON ssm.subject_id = sub.subject_id
            JOIN faculty f ON ssm.employee_id = f.employee_id
            LEFT JOIN student_assignments sa ON ssm.student_id = sa.student_id AND sa.subject_id = ssm.subject_id
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
            LEFT JOIN student_mentoring sm ON mo.option_name = sm.activity_type AND sm.student_id = (SELECT student_id FROM students WHERE roll_number = ?)
            WHERE mo.form_id = (SELECT form_id FROM students WHERE roll_number = ?)";
        $stmt_mentoring = $conn->prepare($query_mentoring);
        $stmt_mentoring->bind_param("ss", $roll_number, $roll_number);
        $stmt_mentoring->execute();
        $result_mentoring = $stmt_mentoring->get_result();
        $mentoring_data = $result_mentoring->fetch_all(MYSQLI_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Home | Resources Information</title>
    <link rel="stylesheet" type="text/css" href="stylesStudent.css">
</head>
<body>
    <div class="content">
        <div style="text-align: center; margin-top:-80px;">
            <img src="Mits_logo_24-removebg-preview.png" alt="Table Image" height="200" width="800">
        </div>
        <div class="heading-section">
            <label><center>Student No Due Form Status</center></label>
        </div>
        <form method="POST" action="">
            <div style='margin-left: 150px;'>
                <label>Roll Number :</label>
                <input type="text" id="roll_number" name="roll_number" style="height: 30px; width: 320px; border:1px solid black;border-radius:5px;" value="<?php echo htmlspecialchars($_POST['roll_number'] ?? ''); ?>" required>
                <button type="submit" class="add-button" style="width: 100px;">Search</button>
            </div>
        </form>
        
        <?php if (!empty($student_data)): ?>
        <div class="form-row">
            <div style="margin-left: 150px;">
                <label>Student Name :</label>
                <input type="text" style="width: 290px; margin-left: 18px;" value="<?php echo htmlspecialchars($student_data['name']); ?>" disabled>
            </div>
            <div>
                <label>Roll Number :</label>
                <input type="text" style="width: 250px; margin-left: 10px;" value="<?php echo htmlspecialchars($student_data['roll_number']); ?>" disabled>
            </div>
        </div>
        <div class="form-row">
            <div style="margin-left: 150px;">
                <label>Year & Semester :</label>
                <input type="text" style="width: 290px;" value="<?php echo htmlspecialchars($student_data['year_semester']); ?>" disabled>
            </div>
            <div>
                <label>Section :</label>
                <input type="text" style="width: 290px;" value="<?php echo htmlspecialchars($student_data['section']); ?>" disabled>
            </div>
        </div>

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
                        <td style="text-align: center;"><?php echo $subject['assignment_1_status'] === 'Yes' ? '✔' : '✘'; ?></td>
                        <td style="text-align: center;"><?php echo $subject['assignment_2_status'] === 'Yes' ? '✔' : '✘'; ?></td>
                        <td><?php echo htmlspecialchars($subject['remarks'] ?? ''); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

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
                        <td><?php echo htmlspecialchars($mentoring['completion_status'] ?? 'Pending'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <p style="color: red; text-align: center;">No student found with the given roll number.</p>
        <?php endif; ?>
    </div>
</body>
</html>
