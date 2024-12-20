<?php
// Include the database connection
include 'dbconn.php';

// Start session
session_start();

// Check if form_id is passed
if (!isset($_GET['form_id'])) {
    die("Invalid request: Form ID is missing.");
}

$form_id = $_GET['form_id'];

// Fetch the year and section from the `no_due_forms` table using the form_id
$sql = "SELECT year_semester, section FROM no_due_forms WHERE form_id = $form_id";
$result = $conn->query($sql);

$yearSemester = '';
$section = '';

if ($result && $result->num_rows > 0) {
    // Fetch the row data
    $row = $result->fetch_assoc();
    $yearSemester = $row['year_semester'];
    $section = $row['section'];
} else {
    // Handle the case where the form_id is invalid or not found
    die("Invalid form ID or data not found.");
}

// Query to check if the form is already mapped
$query = "SELECT COUNT(*) AS student_count FROM students WHERE form_id = '$form_id'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

if ($row['student_count'] > 0) {
    // If the form is mapped, display a SweetAlert2 message with two buttons
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
            // document.addEventListener('DOMContentLoaded', function() {
            //     Swal.fire({
            //         icon: 'info',
            //         title: '<span class=\"custom-title\">Form Mapping Info</span>',
            //         html: '<span class=\"custom-content\">This form is already mapped.</span>',
            //         showCancelButton: true,
            //         confirmButtonText: '<span class=\"custom-button\">See Details</span>',
            //         cancelButtonText: '<span class=\"custom-button\">OK</span>'
            //     }).then(function(result) {
            //         if (result.isConfirmed) {
            //             window.location.href = 'mapedDetails.php?form_id=$form_id'; // Redirect to mapped details
            //         } else {
            //             window.location.href = 'NoDueFormList.php'; // Redirect to the forms list
            //         }
            //     });
            // });
            window.location.href = 'mapedDetails.php?form_id=$form_id';
          </script>";
       
  
    exit;
}


// Proceed with rendering the mapping page since the form is not yet mapped
?>


<!DOCTYPE html>
<html>
<head>
    <title>MITS | NoDueForm</title>
    
    <link rel="stylesheet" type="text/css" href="stylesmap.css">
    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
    <!-- <link rel="stylesheet" href="styles.css"> -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            /* background-color: #f5f5f5;  */
            background: linear-gradient(to bottom right, #d8f0dc, #ffffff);
        }

        /* Main content area */
        .content {
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            margin-top: 60px;
            margin-left: 270px;
        }

        /* Heading section */
        .heading-section label {
            font-size: 32px;
            font-weight: 700; /* Bold weight */
            color: #357EC7;
            margin-bottom: 20px;
            
        }

        .heading-section {
            display: flex;
            align-items: center;
            justify-content: left;
            font-size: 32px;
            font-weight: bold;
            color: #357EC7;
        }

        /* Form row styles */
        .form-row {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-row label {
            min-width: 150px;
        }

        .form-row select, 
        .form-row input {
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100%; /* Ensures full-width on larger screens */
            max-width: 300px; /* Limits input width */
        }

        /* Button styles */
        .buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }

        .buttons button {
            width: 120px;
            height: 40px;
            background-color: #C0C0C0;
            color: #fff;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            border: none;
            transition: background-color 0.3s ease;
        }

        .buttons button:hover {
            background-color: #0056b3;
        }

        /* Table container and table styles */
        .table-container {
            margin: 20px 30px;
            width: 90%;
            font-family: 'Poppins', Arial, sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            border-radius: 6px;
        }

        th, td {
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

        /* Add button styling */
        .add-btn {
            padding: 8px 16px;
            background-color: #007bff;
            color: #fff;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            transition: background-color 0.3s ease;
            text-decoration: none;
            margin : 8px;
            border: 0px;
        }

        .add-btn:hover {
            background-color: #0056b3;
        }

        .upload-container {
            display: flex;
            flex-direction: row; 
            align-items: center; 
            justify-content: center; 
            gap: 15px; 
            padding: 20px;
            margin-top: 20px;
        }

        .upload-container form {
            width: 100%;
            max-width: 500px; 
            text-align: center; 
        }

        .upload-container label {
            font-size: 16px;
            margin-bottom: 10px;
        }

        .upload-container input[type="file"] {
            margin: 10px 0 20px;
        }



        #otherMappingTable input[type="text"] {
            padding: 10px;
            border-radius: 5px;
            border: 0px solid #ced4da;
            font-size: 14px;
            width: 200px;
            max-width: 100%;
        }

        .remove-btn {
            width: 100px;
            font-size: 14px;
            font-family: 'Poppins', sans-serif;
            padding: 6px 6px;
            cursor: pointer;
        }

        #otherMappingTable tr {
            border-bottom: 1px solid #ddd;
        }

        #otherMappingTable tr:hover {
            background-color: #f8f9fa;
        }

        #otherMappingTable td button.remove-btn:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }

        #otherMappingTable input[type="text"]:focus {
            outline: none;
            border: 0px solid #ced4da;
        }

        #facultyMappingTable input[type="text"] {
            padding: 10px;
            border-radius: 5px;
            border: 0px solid #ced4da;
            font-size: 14px;
            width: 200px;
            max-width: 100%;
        }


        .remove-btn {
            width: 100px;
            font-size: 14px;
            font-family: 'Poppins', sans-serif;
            padding: 6px 6px;
            cursor: pointer;
            background-color: #dc3545;
            color: #fff;
            border: 1px;
            border-radius: 5px;
        }

        #facultyMappingTable tr {
            border-bottom: 1px solid #ddd;
        }

        #facultyMappingTable tr:hover {
            background-color: #f8f9fa;
        }

        #facultyMappingTable td button.remove-btn:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }

        #facultyMappingTable input[type="text"]:focus {
            outline: none;
            border: 0px solid #ced4da;
        }
        input[placeholder="Enter Subject Code"] {
            text-transform: uppercase;
        }
    </style>
</head>
<body>



<?php include('sidebar.php'); ?>


<div class="content">
    

    <div style="text-align: center; margin-top:-70px;margin-bottom:15px">
        <img src="Mits_logo_24-removebg-preview.png" alt="MITS Logo" height="200" width="800">
    </div>

    <div class="heading-section">
        <label>Mapping of Subject to Faculty</label>
    </div>
    <form id="mainForm" action="processForm.php" method="POST" enctype="multipart/form-data">

    <input type="hidden" name="form_id" value="<?php echo htmlspecialchars($_GET['form_id']); ?>">

    <!-- General Information -->
    <div class="form-row">
            <div style="margin-left: 100px;">
                <label>Year & Semester</label>
                <!-- Displaying Year & Semester dynamically from the database -->
                <input type="text" id="yearSemester" name="yearSemester" style="font-family: Poppins; font-size: 14px; width: 290px;" value="<?php echo htmlspecialchars($yearSemester); ?>" readonly>
            </div>
            <div>
                <label>Section</label>
                <!-- Displaying Section dynamically from the database -->
                <input type="text" id="section" name="section" style="font-family: Poppins; font-size: 14px; width: 290px;" value="<?php echo htmlspecialchars($section); ?>" readonly>
            </div>
        </div>

    <!-- Faculty Mapping -->
    <label style="display: block; font-size: 20px; color: purple; margin-top: 40px;">Subject to Faculty Mapping Information</label>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="font-family: Poppins; font-size: 16px; width: 10%;">S.No.</th>
                    <th style="font-family: Poppins; font-size: 16px; width: 25%;">Subject Code</th>
                    <th style="font-family: Poppins; font-size: 16px; width: 45%;">Name of the Subject</th>
                    <th style="font-family: Poppins; font-size: 16px;" colspan="2">Employee ID</th>
                </tr>
            </thead>
            <tbody id="facultyMappingTable">
                <tr>
                    <td>1</td>
                    <td><input type="text" style="font-family: Poppins; font-size: 14px;" name="subjectCode[]" placeholder="Enter Subject Code" required></td>
                    <td><input type="text" style="font-family: Poppins; font-size: 14px;" name="subjectName[]" placeholder="Enter Subject Name" required></td>
                    <td><input type="text" style="font-family: Poppins; font-size: 14px;" name="employeeID[]" placeholder="Enter Employee ID" required></td>
                    <td style="text-align: center; vertical-align: middle;">
                        <button type="button" onclick="removeFacultyMappingRow(this)" class="remove-btn">Remove</button>
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td><input type="text" style="font-family: Poppins; font-size: 14px;" name="subjectCode[]" placeholder="Enter Subject Code" required></td>
                    <td><input type="text" style="font-family: Poppins; font-size: 14px;" name="subjectName[]" placeholder="Enter Subject Name" required></td>
                    <td><input type="text" style="font-family: Poppins; font-size: 14px;" name="employeeID[]" placeholder="Enter Employee ID" required></td>
                    <td style="text-align: center; vertical-align: middle;">
                        <button type="button" onclick="removeFacultyMappingRow(this)" class="remove-btn">Remove</button>
                    </td>
                </tr>
                <tr>
                    <td>3</td>
                    <td><input type="text" style="font-family: Poppins; font-size: 14px;" name="subjectCode[]" placeholder="Enter Subject Code" required></td>
                    <td><input type="text" style="font-family: Poppins; font-size: 14px;" name="subjectName[]" placeholder="Enter Subject Name" required></td>
                    <td><input type="text" style="font-family: Poppins; font-size: 14px;" name="employeeID[]" placeholder="Enter Employee ID" required></td>
                    <td style="text-align: center; vertical-align: middle;">
                        <button type="button" onclick="removeFacultyMappingRow(this)" class="remove-btn">Remove</button>
                    </td>
                </tr>
                <tr>
                    <td>4</td>
                    <td><input type="text" style="font-family: Poppins; font-size: 14px;" name="subjectCode[]" placeholder="Enter Subject Code" required></td>
                    <td><input type="text" style="font-family: Poppins; font-size: 14px;" name="subjectName[]" placeholder="Enter Subject Name" required></td>
                    <td><input type="text" style="font-family: Poppins; font-size: 14px;" name="employeeID[]" placeholder="Enter Employee ID" required></td>
                    <td style="text-align: center; vertical-align: middle;">
                        <button type="button" onclick="removeFacultyMappingRow(this)" class="remove-btn">Remove</button>
                    </td>
                </tr>
                <tr>
                    <td>5</td>
                    <td><input type="text" style="font-family: Poppins; font-size: 14px;" name="subjectCode[]" placeholder="Enter Subject Code" required></td>
                    <td><input type="text" style="font-family: Poppins; font-size: 14px;" name="subjectName[]" placeholder="Enter Subject Name" required></td>
                    <td><input type="text" style="font-family: Poppins; font-size: 14px;" name="employeeID[]" placeholder="Enter Employee ID" required></td>
                    <td style="text-align: center; vertical-align: middle;">
                        <button type="button" onclick="removeFacultyMappingRow(this)" class="remove-btn">Remove</button>
                    </td>
                </tr>
                <tr>
                    <td>6</td>
                    <td><input type="text" style="font-family: Poppins; font-size: 14px;" name="subjectCode[]" placeholder="Enter Subject Code" required></td>
                    <td><input type="text" style="font-family: Poppins; font-size: 14px;" name="subjectName[]" placeholder="Enter Subject Name" required></td>
                    <td><input type="text" style="font-family: Poppins; font-size: 14px;" name="employeeID[]" placeholder="Enter Employee ID" required></td>
                    <td style="text-align: center; vertical-align: middle;">
                        <button type="button" onclick="removeFacultyMappingRow(this)" class="remove-btn">Remove</button>
                    </td>
                </tr>
            </tbody>
        </table>
        <button type="button" onclick="addFacultyMappingRow()" class="add-btn">Add More</button>
    </div>

    <!-- Mentoring Information -->
    <label style="font-size: 20px; color: purple; margin-top: 40px;">Mentoring Information</label>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>S.No.</th>
                    <th>Additional Information</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="mentoringTable">
                <!-- Static Mentor Activity Information -->
                <tr>
                    <td>1</td>
                    <td>Student Achievements (IELTS / BEC / Foreign Language / Workshop / Conference / SIH / Publication etc.)</td>
                    <td></td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>NASSCOM Certification</td>
                    <td></td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>Course Exit Survey</td>
                    <td></td>
                </tr>
                <tr>
                    <td>4</td>
                    <td>AICTE 360 Feedback</td>
                    <td></td>
                </tr>
                <tr>
                    <td>5</td>
                    <td>Mentor Mentee Meeting</td>
                    <td></td>
                </tr>
                <tr>
                    <td>6</td>
                    <td>NPTEL Certificate</td>
                    <td></td>
                </tr>
                <tr>
                    <td>7</td>
                    <td>Soft Skills</td>
                    <td></td>
                </tr>
                <tr>
                    <td>8</td>
                    <td>Skill Oriented Course</td>
                    <td></td>
                </tr>
                <tr>
                    <td>9</td>
                    <td><input type="text" name="mentoringActivities[]" placeholder="Activity Name" style="font-family: Poppins; font-size: 16px; border: none; outline: none;" required></td>
                    <td style="text-align: center; vertical-align: middle;">
                        <button type="button" onclick="removeMentoringRow(this)" class="remove-btn">Remove</button>
                    </td>
                </tr>
                
            </tbody>
        </table>
        <button type="button" onclick="addMentoringRow()" class="add-btn">Add More</button>
    </div>

    <!-- Upload Section -->
    <label style="font-size: 20px; color: purple; margin-top: 40px;">Upload Data</label>
    <div style="margin-right: 105px;" class="upload-container">
        <label for="studentListFile">Upload Students Data List (.xlsx format & avoid incompleted rows):</label>
        <input type="file" id="studentListFile" name="studentListFile" accept=".xlsx" required>
        <a href="studentdata.xlsx" download class="add-btn">Download Template</a>
    </div>

    <!-- Submit Buttons -->
    <div style="display: flex; justify-content: center; margin-top: 20px; margin-right: 60px;">
        <button type="button" onclick="window.location.href='NoDueFormList.php'" class="add-btn">Home</button>
        <button type="submit" class="add-btn">Submit</button>
    </div>

    <div class="footer-row" style="font-size: 14px; text-align: center; height: 24px; margin-top: 30px; background: linear-gradient(to left, #c3e1cb, #ffffff); color: #333; padding: 5px; margin-right:2.5vw;border-radius:10px;">
        Developed & Hosted by <b>MITS_InstituteDatabaseSystem@PAARC</b>
    </div>
</div>

</form>
    </div>
        
    </div>
    



<script>

function addFacultyMappingRow() {
    let table = document.getElementById('facultyMappingTable');
    let rowCount = table.rows.length; // Get the total number of rows in the table
    let row = table.insertRow(rowCount); // Insert a new row at the end
    row.innerHTML = `
        <td>${rowCount + 1}</td>
        <td><input type="text" style="font-family: Poppins; font-size: 14px;" name="subjectCode[]" placeholder="Enter Subject Code" required></td>
        <td><input type="text" style="font-family: Poppins; font-size: 14px;" name="subjectName[]" placeholder="Enter Subject Name" required></td>
        <td><input type="text" style="font-family: Poppins; font-size: 14px;" name="employeeID[]" placeholder="Enter Employee ID" required></td>
        <td style="text-align: center; vertical-align: middle;">
            <button type="button" onclick="removeFacultyMappingRow(this)" class="remove-btn">Remove</button>
        </td>`;
}

// Function to remove a row
function removeFacultyMappingRow(button) {
    let table = document.getElementById('facultyMappingTable');
    let row = button.closest('tr'); // Get the row of the clicked button
    table.deleteRow(row.rowIndex - 1); // Remove the row
    renumberFacultyRows(); // Renumber the rows
}

// Function to renumber rows after deletion
function renumberFacultyRows() {
    let table = document.getElementById('facultyMappingTable');
    Array.from(table.rows).forEach((row, index) => {
        row.cells[0].textContent = index + 1; // Update the S.No. column
    });
}

function addMentoringRow() {
    let table = document.getElementById('mentoringTable');
    let rowCount = table.rows.length; // Get the total number of rows in the table
    let row = table.insertRow(rowCount); // Insert a new row at the end
    row.innerHTML = `
        <td>${rowCount + 1}</td>
        <td><input type="text" name="mentoringActivities[]" placeholder="Activity Name" style="font-family: Poppins; font-size: 16px; border: none; outline: none;" required></td>
        <td style="text-align: center; vertical-align: middle;">
            <button type="button" onclick="removeMentoringRow(this)" class="remove-btn">Remove</button>
        </td>`;
}

// Function to remove a mentoring row
function removeMentoringRow(button) {
    let table = document.getElementById('mentoringTable');
    let row = button.closest('tr'); // Get the row of the clicked button
    table.deleteRow(row.rowIndex - 1); // Remove the row
    renumberMentoringRows(); // Renumber the rows
}

// Function to renumber rows after deletion
function renumberMentoringRows() {
    let table = document.getElementById('mentoringTable');
    Array.from(table.rows).forEach((row, index) => {
        row.cells[0].textContent = index + 1; // Update the S.No. column
    });
}

    
</script>

</body>
</html>