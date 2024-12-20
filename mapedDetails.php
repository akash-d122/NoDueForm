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

    // Fetch Year and Section information linked to the form
    $query_year_section = "
        SELECT DISTINCT year_semester, section 
        FROM no_due_forms 
        WHERE form_id = '$form_id' LIMIT 1";
    $result_year_section = mysqli_query($conn, $query_year_section);

    if ($row = mysqli_fetch_assoc($result_year_section)) {
        $yearSemester = $row['year_semester'];
        $section = $row['section'];
    }

    // Fetch Faculty details linked to the specific form_id
    $query_subjects = "
    SELECT s.subject_name, f.name AS faculty_name
    FROM subject_faculty_mapping sfm
    INNER JOIN subjects s ON sfm.subject_id = s.subject_id
    INNER JOIN faculty f ON sfm.employee_id = f.employee_id
    WHERE s.form_id = '$form_id'
";


$result_subjects = mysqli_query($conn, $query_subjects);
if (!$result_subjects) {
    die("Error in query: " . mysqli_error($conn));
}


    // Fetch Student Assignment and Mentor Status linked to the form_id
    $query_students = "
        SELECT s.student_id, s.roll_number AS student_name, 
               sa.assignment_1_status, sa.assignment_2_status, 
               sm.completion_status 
        FROM students s
        JOIN student_assignments sa ON s.student_id = sa.student_id
        JOIN student_mentoring sm ON s.student_id = sm.student_id
        WHERE s.form_id = '$form_id'";

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
    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
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
            margin-left: 10%;
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

        #myBtn {
            font-family: Poppins, sans-serif;
            display: none;
            position: fixed;
            bottom: 15px;
            right: 15px;
            z-index: 99;
            border: none;
            outline: none;
            background-color: #333;
            color: white;
            cursor: pointer;
            padding: 12px 18px;
            border-radius: 50px; /* Circular style for a sleek look */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            font-size: 16px;
        }

        #myBtn:hover {
            background-color: #444;
        }


    </style>
</head>
<body>
    <!-- Sidebar -->
        <?php include('sidebar.php'); ?>

    <div class="content" style="margin-left : 13vw;">
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
        <div class="table-container" style=" width:100%;">
            <table align-items="center">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th style="widht:25vw;">Subject</th>
                        <th>Faculty Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sno = 1; // Initialize the counter
                    while ($row = mysqli_fetch_assoc($result_subjects)) {
                        echo "<tr>";
                        echo "<td >" . $sno++ . "</td>"; // Increment the counter
                        echo "<td>" . htmlspecialchars($row['subject_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['faculty_name']) . "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>


        <!-- Student Assignment and Mentor Status Table -->
        <div class="table-container" style="margin-right:0vw; width:100%">
            <table align-items="center">
                <thead>
                    <tr style="text-align: center;">
                        <th style="text-align: left;">S.No.</th>
                        <th style="text-align: left;">Roll Number</th>
                        <th>Assignment 1 Status</th>
                        <th>Assignment 2 Status</th>
                        <th>Mentor Status</th>
                        <th>Approved/Pending</th>
                    </tr>
                </thead>
                <tbody style="text-align: center;">
                    <?php
                    $sno = 1; // Initialize serial number counter
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
                        // Serial Number
                        echo "<td style='text-align: center;'>" . $sno++ . "</td>";

                        // Make the roll number a clickable link
                        echo "<td style='text-align: left;'>" . 
                            "<a style='text-decoration: none;' href='student1.php?roll_number=" . urlencode($student_data['name']) . "'>" . 
                            htmlspecialchars($student_data['name']) . 
                            "</a></td>";

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

        <div style="text-align: center; margin-top: 20px;">
                <button onclick="window.location.href='NoDueFormList.php'" style="padding: 10px 20px; font-size: 16px; background-color: #357EC7; color: white; border: none; border-radius: 5px; cursor: pointer;">
                    Back
                </button>
                <button id="myBtn" onclick="scrollToTop()">↑ Top</button>    
        </div>
    </div>

    <script>
        // let mybutton = document.getElementById("myBtn");

        window.onscroll = function () { scrollFunction(); };

        function scrollFunction() {
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                mybutton.style.display = "block";
            } else {
                mybutton.style.display = "none";
            }
        }

        function topFunction() {
            document.body.scrollTop = 0;
            document.documentElement.scrollTop = 0;
        }
    </script>
</body>
</html>