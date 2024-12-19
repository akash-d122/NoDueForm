<?php
    // Include the database connection
    include 'dbconn.php';

    // Initialize variables
    $yearSemester = '';
    $section = '';
    $form_id = isset($_GET['form_id']) ? $_GET['form_id'] : null;

    if (!$form_id) {
        die("Error: Form ID not provided.");
    }

    // Fetch Year and Section information
    $query_year_section = "SELECT DISTINCT year_semester, section FROM no_due_forms WHERE form_id = '$form_id' LIMIT 1";
    $result_year_section = mysqli_query($conn, $query_year_section);

    if ($row = mysqli_fetch_assoc($result_year_section)) {
        $yearSemester = $row['year_semester'];
        $section = $row['section'];
    }

    // Fetch Subject and Faculty Mapping
    $query_subjects = "SELECT s.subject_name, f.name AS faculty_name 
                    FROM subject_faculty_mapping sfm 
                    JOIN subjects s ON sfm.subject_id = s.subject_id 
                    JOIN faculty f ON sfm.employee_id = f.employee_id";
    $result_subjects = mysqli_query($conn, $query_subjects);

    // Fetch Student Assignment and Mentor Status
    $query_students = "
        SELECT s.student_id, s.name AS student_name, 
            sa.assignment_1_status, sa.assignment_2_status, 
            sm.completion_status 
        FROM students s
        JOIN student_assignments sa ON s.student_id = sa.student_id
        JOIN student_mentoring sm ON s.student_id = sm.student_id
        WHERE s.form_id = '$form_id' 
    ";
    $result_students = mysqli_query($conn, $query_students);

    // Aggregating student data
    $students = [];
    while ($row = mysqli_fetch_assoc($result_students)) {
        $students[$row['student_id']]['name'] = $row['student_name'];
        $students[$row['student_id']]['assignment_1_status'][] = $row['assignment_1_status'];
        $students[$row['student_id']]['assignment_2_status'][] = $row['assignment_2_status'];
        $students[$row['student_id']]['mentor_status'][] = $row['completion_status'];
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <title>Mapped Details</title>
    <style>
        body {
            background: linear-gradient(to bottom right, #c3e6cb, #ffffff);
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover;
            font-family: Arial, sans-serif;
        }

        .content {
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            margin-top: 60px;
        }

        .heading-section {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: bold;
            color: #357EC7;
        }
        .ex {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 50px;
            margin-top: -5%;
            color: #d45454;
        }

        .table-container {
            margin: 20px 30px;
            font-family: 'Poppins', Arial, sans-serif;
        }

        table {
            width: 80%;
            border-collapse: collapse;
            text-align: left;
            border-radius: 10px;
            margin-left: 10%;
        }

        th,
        td {
            padding: 12px;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        td {
            background-color: #fff;
        }

        td input[type="checkbox"] {
            width: 20px;
            height: 20px;
        }

        .th1 {
            border: none;
            background-color: transparent;
            font-size: 20px;
            font-weight: normal;
            color: #000;
        }

        .th1 th {
            border: none;
            background-color: transparent;
        }

        .form-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .form-row div {
            width: 45%;
        }

        .form-row label {
            font-family: Poppins, sans-serif;
            font-size: 14px;
            color: #357EC7;
        }

        .form-row input {
            font-family: Poppins, sans-serif;
            font-size: 14px;
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .year-section {
            text-align: center;
            margin-top: 20px;
            font-size: 16px;
            color: #357EC7;
            font-family: Poppins, sans-serif;
        }

        .table-container {
            margin-top: 40px;
        }
    </style>
</head>
<body>

    <div class="content">
        <div class="ex">
            <img src="Mits_logo_24-removebg-preview.png" alt="MITS Logo" height="200" width="800">
        </div>
        <div class="heading-section">
            <label style="margin-left : 2vw;">NoDue Issued Status</label>
        </div>

        <!-- Year and Section Information -->
        <div class="form-row" style="margin-left : 15vw; margin-top : 5vh;">
            <div>
                <label style="font-size : 20px;">Year & Semester : </label>
                <!-- Displaying Year & Semester dynamically from the database -->
                <input type="text" id="yearSemester" name="yearSemester" style="width:10vw" value="<?php echo htmlspecialchars($yearSemester); ?>" readonly>
            </div>
            <div>
                <label style="font-size : 20px;">Section : </label>
                <!-- Displaying Section dynamically from the database -->
                <input type="text" id="section" name="section" style="width:10vw"  value="<?php echo htmlspecialchars($section); ?>" readonly>
            </div>
        </div>

        <!-- Display Year and Section information dynamically
        <div class="year-section">
            Year: <?php echo htmlspecialchars($yearSemester); ?> | Section: <?php echo htmlspecialchars($section); ?>
        </div> -->

        <!-- Subject and Faculty Table -->
        <div class="table-container">
            <table align-items="center">
                <thead>
                    <tr style="text-align : center;">
                        <th>Subject</th>
                        <th>Faculty Name</th>
                    </tr>
                </thead>
                <tbody style="text-align : center;">
                    <?php
                    while ($row = mysqli_fetch_assoc($result_subjects)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['subject_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['faculty_name']) . "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Student Assignment and Mentor Status Table -->
        <div class="table-container">
            <table align-items="center">
                <thead>
                    <tr style="text-align : center;">
                        <th>Student Name</th>
                        <th>Assignment 1 Status</th>
                        <th>Assignment 2 Status</th>
                        <th>Mentor Status</th>
                        <th>Approved/Pending</th>
                    </tr>
                </thead>
                <tbody style="text-align : center;">
                    <?php
                    foreach ($students as $student_id => $student_data) {
                        // Check if all assignment 1 statuses are approved (1)
                        $assignment_1_approved = !in_array(0, $student_data['assignment_1_status']);

                        // Check if all assignment 2 statuses are approved (1)
                        $assignment_2_approved = !in_array(0, $student_data['assignment_2_status']);

                        // Check if all mentor statuses are "Yes" or "NA"
                        $mentor_approved = !in_array("NULL", $student_data['mentor_status']) && 
                                           count(array_intersect($student_data['mentor_status'], ['Yes', 'NA'])) === count($student_data['mentor_status']);

                        // Final status: Approved if all assignments and mentoring are approved
                        $final_status = ($assignment_1_approved && $assignment_2_approved && $mentor_approved) ? 'Approved' : 'Pending';

                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($student_data['name']) . "</td>";

                        // Assignment 1 status icon
                        echo "<td style='text-align: center;'>" . 
                            ($assignment_1_approved ? 
                                '<i class="fa-regular fa-circle-check fa-xl" style="color: #63E6BE;"></i>' : 
                                '<i class="fa-solid fa-spinner fa-rotate-90 fa-xl" style="color: #25378d;"></i>') . 
                            "</td>";

                        // Assignment 2 status icon
                        echo "<td style='text-align: center;'>" . 
                            ($assignment_2_approved ? 
                                '<i class="fa-regular fa-circle-check fa-xl" style="color: #63E6BE;"></i>' : 
                                '<i class="fa-solid fa-spinner fa-rotate-90 fa-xl" style="color: #25378d;"></i>') . 
                            "</td>";

                        // Mentor status icon
                        echo "<td style='text-align: center;'>" . 
                            ($mentor_approved ? 
                                '<i class="fa-regular fa-circle-check fa-xl" style="color: #63E6BE;"></i>' : 
                                '<i class="fa-solid fa-spinner fa-rotate-90 fa-xl" style="color: #25378d;"></i>') . 
                            "</td>";

                        // Final approval status with icon
                        echo "<td style='text-align: center;'>" . 
                            ($final_status === 'Approved' ? 
                                '<i class="fa-regular fa-circle-check fa-xl" style="color: #63E6BE;"></i>' : 
                                '<i class="fa-solid fa-spinner fa-rotate-90 fa-xl" style="color: #25378d;"></i>') . 
                            "</td>";

                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </div>

</body>
</html>