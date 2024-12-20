<!DOCTYPE html>
<html>
<head>
    <title>MITS | NoDueForm</title>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="stylesmap.css">
</head>
<body>


<div class="sidebar">
    <!-- Sidebar content can go here (optional) -->
</div>

<div class="content">
    

    <div style="text-align: center; margin-top:-30px;margin-bottom:15px">
        <img src="Mits_logo_24.jpg" alt="MITS Logo" height="200" width="800">
    </div>

    <div class="heading-section">
        <label>Mapping of Subject to Faculty</label>
    </div>
    <form>
        <div class="form-row">
            <div style="margin-left: 175px;">
                <label>Year & Semester</label>
                <input type="text" id="yearSemester" name="yearSemester" style="width: 290px;" readonly>
            </div>

            <div>
                <label>Section</label>
                <input type="text" id="section" name="section" style="width: 290px;" readonly>
            </div>
        </div>

        <label style="display: block; font-size: 20px; color: purple; margin-top: 40px;">Subject to Faculty Mapping Information</label>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 10%;">S.No.</th>
                        <th style="width: 25%;">Subject Code</th>
                        <th style="width: 45%;">Name of the Subject</th>
                        <th colspan="2">Employee ID</th>
                    </tr>
                </thead>
                <tbody id="facultyMappingTable">
                    <tr>
                        <td>1</td>
                        <td><input type="text" placeholder="Enter Subject Code"></td>
                        <td><input type="text" placeholder="Enter Subject Name"></td>
                        <td><input type="text" placeholder="Enter Employee ID"></td>
                        <td style="text-align: center; vertical-align: middle;">
                            <button type="button" onclick="removeFacultyMappingRow(this)" class="remove-btn">Remove</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <button type="button" onclick="addFacultyMappingRow()" class="add-btn">Add More</button>
        </div>

        <label style="font-size: 20px; color: purple; margin-top: 40px;">Mentoring Information</label>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Additional Information</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Student Achievements (IELTS / BEC / Foreign Language / Workshop / Conference / SIH / Publication etc.,)</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>NASSCOM Certification</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Course Exit Survey</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>AICTE 360 Feedback</td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td>Mentor Mentee Meeting</td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td>NPTEL Certificate</td>
                    </tr>
                    <tr>
                        <td>7</td>
                        <td>Soft Skills</td>
                    </tr>
                    <tr>
                        <td>8</td>
                        <td>Skill Oriented Course</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <label style="font-size: 20px; color: purple; margin-top: 40px;">Other Mapping Information</label>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 135px;">S.No.</th>
                        <th colspan="2" style="width: 300px;">Additional Information</th>
                    </tr>
                </thead>
                <tbody id="otherMappingTable">
                    <tr>
                        <td>1</td>
                        <td><input type="text" placeholder="Enter Activity Name"></td>
                        <td style="text-align: center; vertical-align: middle;">
                            <button type="button" onclick="removeOtherMappingRow(this)" class="remove-btn">Remove</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <button type="button" onclick="addOtherMappingRow()" class="add-btn">Add More</button>
        </div>

        <label style="font-size: 20px; color: purple; margin-top: 40px;">Upload Data</label>
        <div class="upload-container">
            <form id="studentListForm" action="" method="POST" enctype="multipart/form-data">
                <label for="studentListFile">Upload Students Data List (.xlsx format only):</label>
                <input type="file" id="studentListFile" name="studentListFile" accept=".xlsx" required>
                <a href="templates/studentdata.xlsx" download class="add-btn">Download Template</a>
                <!-- <button type="submit" class="add-btn">Upload</button> -->
            </form>
        </div>

        <div style="display: flex; justify-content: center; margin-top: 20px;margin-right:60px">
            <button type="button" onclick="window.location.href='dashboard.php'" class="add-btn">Home</button>
            <button type="button" onclick="window.location.href='NoDueFormList.php'" class="add-btn">Submit</button>
            
        </div>
    </form>
</div>

<script>
    function addFacultyMappingRow() {
        let table = document.getElementById('facultyMappingTable');
        let rowCount = table.rows.length;
        let row = table.insertRow(rowCount);
        row.innerHTML = <td>${rowCount + 1}</td>
                        <td><input type="text" placeholder="Enter Subject Code"></td>
                        <td><input type="text" placeholder="Enter Subject Name"></td>
                        <td><input type="text" placeholder="Enter Employee ID"></td>
                        <td style="text-align: center; vertical-align: middle;">
                            <button type="button" onclick="removeFacultyMappingRow(this)" class="remove-btn">Remove</button>
                        </td>;
    }

    function removeFacultyMappingRow(button) {
        let row = button.closest('tr');
        row.remove();
    }

    function addOtherMappingRow() {
        let table = document.getElementById('otherMappingTable');
        let rowCount = table.rows.length;
        let row = table.insertRow(rowCount);
        row.innerHTML = <td>${rowCount + 1}</td>
                        <td><input type="text" placeholder="Enter Activity Name"></td>
                        <td style="text-align: center; vertical-align: middle;">
                            <button type="button" onclick="removeOtherMappingRow(this)" class="remove-btn">Remove</button>
                        </td>;
    }

    function removeOtherMappingRow(button) {
        let row = button.closest('tr');
        row.remove();
    }
</script>

</body>
</html>
