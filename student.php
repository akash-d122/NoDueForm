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
    <title>Home | Student Information</title>
    <link rel="stylesheet" type="text/css" href="stylesStudent.css">
    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>


    </style>
</head>
<body>
    <div class="content">
        <div style="text-align: center; margin-bottom: 20px;">
            <img src="Mits_logo_24-removebg-preview.png" alt="Table Image" height="200" width="800">
        </div>
        <div class="heading-section">
            <h2 style="font-weight: Bold; font-size: 28px; text-align: center; color:#357EC7;">Status of Student No Due Form</h2>
            <!-- <label style="font-weight: bold; align: left;">Status of Student No Due Form</label> -->
        </div>

        <form method="POST" action="">
            <label for="roll_number" style="margin-top:0.6vh; font-size:1vw;margin-left:-5px">Roll Number : </label>
            <input type="text" id="roll_number"  style="width: 600px; text-transform: uppercase; border: 1px solid;" name="roll_number" 
                value="<?php echo htmlspecialchars($_POST['roll_number'] ?? ''); ?>" required>
            <button type="submit">Search</button>
        </form>
    <div style="margin-left : 2vw;">
        <?php if (!empty($student_data)): ?>
        <div class="form-row" style="m">
            <div>
                <label style="color:#357EC7;">Student Name   :</label>
                <input type="text" style="width: 200px; margin-left: 20px; border: none; font-weight: bold; color: black;" value="<?php echo htmlspecialchars($student_data['name']); ?>" disabled>
            </div>
            <div>
                <label style="color:#357EC7;">Roll Number   :</label>
                <input type="text" style="width: 175px; border: none; font-weight: bold; color: black;" value="<?php echo htmlspecialchars($student_data['roll_number']); ?>" disabled>
            </div>
        </div>
        <div class="form-row">
            <div>
                <label style="color:#357EC7;">Year & Semester : </label>
                <input type="text" style="width: 175px; border: none; font-weight: bold; color: black;" value="<?php echo htmlspecialchars($student_data['year_semester']); ?>" disabled>
            </div>
            <div>
                <label style="color:#357EC7;">Section   :</label>
                <input type="text" style="width: 175px; margin-left: 40px; border: none; font-weight: bold; color: black;" value="<?php echo htmlspecialchars($student_data['section']); ?>" disabled>
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
                    <td style="text-align: center;">
                        <?php
                        if (intval($subject['assignment_1_status']) == 1) {
                            echo '<i class="fa-regular fa-circle-check fa-xl" style="color: #63E6BE;"></i><br>Approved';
                        } else {
                            echo '<i class="fa-solid fa-spinner fa-rotate-90 fa-xl" style="color: #25378d;"></i><br>Pending';
                        }
                        ?>
                    </td>
                    <td style="text-align: center;">
                        <?php
                        if (intval($subject['assignment_2_status']) == 1) {
                            echo '<i class="fa-regular fa-circle-check fa-xl" style="color: #63E6BE;"></i><br>Approved';
                        } else {
                            echo '<i class="fa-solid fa-spinner fa-rotate-90 fa-xl" style="color: #25378d;"></i><br>Pending';
                        }
                        ?>
                    </td>
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
                        <td style="text-align: center; width:5vw;">
                            <?php
                            if ($mentoring['completion_status'] === "Yes") {
                                echo '<i class="fa-regular fa-circle-check fa-xl" style="color: #63E6BE;"></i><br><span style="font-size:0.65vw;">Approved</span>';
                            } elseif ($mentoring['completion_status'] === "NA") {
                                echo '<i class="fa-solid fa-circle-question fa-xl" style="color: #FFA500;"></i><br><span style="font-size:0.65vw;">Not Applicable</span>';
                            } else {
                                echo '<i class="fa-solid fa-spinner fa-rotate-90 fa-xl" style="color: #25378d;"></i><br>Pending';
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <div class="footer-row" style="font-size: 14px; text-align: center; height: 24px; margin-top: 30px; color: #333; padding: 5px; margin-right: 2vw;">
                Note: The above information is provisional and provided for reference purposes only.                
            </div>
            <div class="footer-row" style="font-size: 14px; text-align: center; height: 24px; margin-top: 30px; background: linear-gradient(to left, #c3e1cb, #ffffff); color: #333; padding: 5px; margin-right:2.5vw;">
        Developed & Hosted by <b>MITS_InstituteDatabaseSystem@PAARC</b>
    </div>
        </div>
        <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <p class="no-data">No student found with the given roll number.</p>
        <?php endif; ?>
    </div>
    
        </div>
</body>
</html>
