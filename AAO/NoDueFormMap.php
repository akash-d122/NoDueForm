<!DOCTYPE html>
<html>
<head>
    <title>Home | Resources Information</title>
    <link rel="stylesheet" type="text/css" href="stylesmap.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Add jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="sidebar">
        <div class="profile">
            <!-- <div class="icon">
                <i class="fas fa-user"></i>
            </div>
            <div class="username">
                Admin
            </div> -->
        </div>
        <!-- <div class="nav">
            <ul>
                <li><a href="#"><i class="fas fa-home icon"></i>Home</a></li>
                <li><a href="booking.php"><i class="fas fa-bell icon"></i>Booking Page</a></li>
                <li><a href="department.php"><i class="fas fa-building icon"></i>Department-wise</a></li>
                <li><a href="hall.php"><i class="fas fa-door-open icon"></i>Hall-wise</a></li>
            </ul>
        </div> -->
    </div>

    <div class="content">
        <div class="heading-section">
            <label>Mapping of Subject to Faculty</label>
        </div>

        <div style="text-align: center; margin: 20px 0;">
            <img src="Mits_logo_24.jpg" alt="Logo" height="200" width="800">
        </div>

        <form id="uploadForm" action="uploadStudents.php" method="POST" enctype="multipart/form-data" class="form-section" style="margin: 20px;">
            <label style="font-family: Poppins; color: purple; font-size: 20px; margin-left: 175px;">Upload Student Excel File:</label>
            <div style="display: flex; align-items: center; justify-content: center; margin-top: 10px;">
                <!-- Hidden input to send form_id -->
                <input type="hidden" name="form_id" id="form_id" value="">

                <input type="file" name="student_file" required accept=".xlsx" 
                    style="width: 50%; padding: 8px; font-family: Poppins; border: 1px solid purple; border-radius: 5px;">
                <button type="submit" style="margin-left: 20px; background-color: purple; color: white; border: none; padding: 10px 20px; font-family: Poppins; cursor: pointer;">Upload</button>
            </div>
        </form>

        <!-- Success Message -->
        <div id="responseMessage" style="text-align: center; font-size: 18px; margin-top: 20px;"></div>
    


        <form action="saveMappingData.php" method="POST">
            <!-- Subject to Faculty Mapping -->
            <label style="font-family: Poppins; color: purple; font-size: 20px; margin-left: 175px;">Subject to Faculty Mapping</label>
            <div class="table-container">
                <table id="subjectFacultyTable">
                    <thead>
                        <tr>
                            <th style="font-family: Poppins; color: purple">S.No.</th>
                            <th style="font-family: Poppins; color: purple">Name of the Subject</th>
                            <th style="font-family: Poppins; color: purple">Faculty Name</th>
                            <th style="font-family: Poppins; color: purple">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td><input type="text" name="subject_name[]" placeholder="Enter Subject Name" required style="width: 100%; height: 30px; font-family: Poppins;"></td>
                            <td><input type="text" name="faculty_name[]" placeholder="Enter Faculty Name" required style="width: 100%; height: 30px; font-family: Poppins;"></td>
                            <td><button type="button" onclick="removeRow(this)" style="background-color: purple; color: white; border: none; font-family: Poppins; padding: 5px 10px; cursor: pointer;">Remove</button></td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" onclick="addRow('subjectFacultyTable')" style="margin-top: 10px; background-color: #007bff; color: white; border: none; padding: 8px 16px; font-family: Poppins; cursor: pointer;">Add More</button>
            </div>

            <!-- Optional Activities Mapping -->
            <label style="font-family: Poppins; color: purple; font-size: 20px; margin-left: 175px;">Optional Activities Mapping</label>
            <div class="table-container">
                <table id="optionalActivitiesTable">
                    <thead>
                        <tr>
                            <th style="font-family: Poppins; color: purple">S.No.</th>
                            <th style="font-family: Poppins; color: purple">Activity Name</th>
                            <th style="font-family: Poppins; color: purple">Faculty Name</th>
                            <th style="font-family: Poppins; color: purple">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Predefined Activity Names -->
                        <tr>
                            <td>1</td>
                            <td><input type="text" name="activity_1" value="Workshops" disabled style="width: 100%; height: 30px; font-family: Poppins;"></td>
                            <td><input type="text" name="faculty_activity_1" placeholder="Enter Faculty Name" required style="width: 100%; height: 30px; font-family: Poppins;"></td>
                            <td><button type="button" onclick="removeRow(this)" style="background-color: purple; color: white; border: none; font-family: Poppins; padding: 5px 10px; cursor: pointer;">Remove</button></td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td><input type="text" name="activity_2" value="Guest Lectures" disabled style="width: 100%; height: 30px; font-family: Poppins;"></td>
                            <td><input type="text" name="faculty_activity_2" placeholder="Enter Faculty Name" required style="width: 100%; height: 30px; font-family: Poppins;"></td>
                            <td><button type="button" onclick="removeRow(this)" style="background-color: purple; color: white; border: none; font-family: Poppins; padding: 5px 10px; cursor: pointer;">Remove</button></td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td><input type="text" name="activity_3" value="Seminars" disabled style="width: 100%; height: 30px; font-family: Poppins;"></td>
                            <td><input type="text" name="faculty_activity_3" placeholder="Enter Faculty Name" required style="width: 100%; height: 30px; font-family: Poppins;"></td>
                            <td><button type="button" onclick="removeRow(this)" style="background-color: purple; color: white; border: none; font-family: Poppins; padding: 5px 10px; cursor: pointer;">Remove</button></td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td><input type="text" name="activity_4" value="Hackathons" disabled style="width: 100%; height: 30px; font-family: Poppins;"></td>
                            <td><input type="text" name="faculty_activity_4" placeholder="Enter Faculty Name" required style="width: 100%; height: 30px; font-family: Poppins;"></td>
                            <td><button type="button" onclick="removeRow(this)" style="background-color: purple; color: white; border: none; font-family: Poppins; padding: 5px 10px; cursor: pointer;">Remove</button></td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td><input type="text" name="activity_5" value="Social Service Programs" disabled style="width: 100%; height: 30px; font-family: Poppins;"></td>
                            <td><input type="text" name="faculty_activity_5" placeholder="Enter Faculty Name" required style="width: 100%; height: 30px; font-family: Poppins;"></td>
                            <td><button type="button" onclick="removeRow(this)" style="background-color: purple; color: white; border: none; font-family: Poppins; padding: 5px 10px; cursor: pointer;">Remove</button></td>
                        </tr>
                        <tr>
                            <td>6</td>
                            <td><input type="text" name="activity_6" value="Sports Events" disabled style="width: 100%; height: 30px; font-family: Poppins;"></td>
                            <td><input type="text" name="faculty_activity_6" placeholder="Enter Faculty Name" required style="width: 100%; height: 30px; font-family: Poppins;"></td>
                            <td><button type="button" onclick="removeRow(this)" style="background-color: purple; color: white; border: none; font-family: Poppins; padding: 5px 10px; cursor: pointer;">Remove</button></td>
                        </tr>
                        <tr>
                            <td>7</td>
                            <td><input type="text" name="activity_7" value="Cultural Events" disabled style="width: 100%; height: 30px; font-family: Poppins;"></td>
                            <td><input type="text" name="faculty_activity_7" placeholder="Enter Faculty Name" required style="width: 100%; height: 30px; font-family: Poppins;"></td>
                            <td><button type="button" onclick="removeRow(this)" style="background-color: purple; color: white; border: none; font-family: Poppins; padding: 5px 10px; cursor: pointer;">Remove</button></td>
                        </tr>
                        <tr>
                            <td>8</td>
                            <td><input type="text" name="activity_8" value="Research Projects" disabled style="width: 100%; height: 30px; font-family: Poppins;"></td>
                            <td><input type="text" name="faculty_activity_8" placeholder="Enter Faculty Name" required style="width: 100%; height: 30px; font-family: Poppins;"></td>
                            <td><button type="button" onclick="removeRow(this)" style="background-color: purple; color: white; border: none; font-family: Poppins; padding: 5px 10px; cursor: pointer;">Remove</button></td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" onclick="addRow('optionalActivitiesTable')" style="margin-top: 10px; background-color: #007bff; color: white; border: none; padding: 8px 16px; font-family: Poppins; cursor: pointer;">Add More Activities</button>
            </div>

            <!-- Submit Mapping Form -->
            <div style="display: flex; align-items: center; gap: 20px; margin-left: 300px; margin-top: 20px;">
            <button type="button" style="font-family: Poppins; width:120px; height:40px; margin-left: 300px;" onclick="window.location.href='searchstudent.php';">Home</button>
            <button type="button" style="font-family: Poppins; width:120px; height:40px;" class="save-btn">Save</button>
            <button type="button" style="font-family: Poppins; width:120px; height:40px;" class="logout-btn" onclick="window.location.href='logout.php';">Logout</button>
        </div>
        </form>
    </div>

    <script>
        // JavaScript function to dynamically add and remove rows in tables
        function addRow(tableId) {
            var table = document.getElementById(tableId);
            var rowCount = table.rows.length;
            var row = table.insertRow(rowCount);
            var cell1 = row.insertCell(0);
            var cell2 = row.insertCell(1);
            var cell3 = row.insertCell(2);
            var cell4 = row.insertCell(3);

            cell1.innerHTML = rowCount + 1;
            cell2.innerHTML = '<input type="text" name="activity_' + rowCount + '" placeholder="Enter Activity Name" style="width: 100%; height: 30px; font-family: Poppins;" required>';
            cell3.innerHTML = '<input type="text" name="faculty_activity_' + rowCount + '" placeholder="Enter Faculty Name" style="width: 100%; height: 30px; font-family: Poppins;" required>';
            cell4.innerHTML = '<button type="button" onclick="removeRow(this)" style="background-color: purple; color: white; border: none; font-family: Poppins; padding: 5px 10px; cursor: pointer;">Remove</button>';
        }

        // Function to remove a row
        function removeRow(button) {
            var row = button.closest("tr");
            row.parentNode.removeChild(row);
        }

        const urlParams = new URLSearchParams(window.location.search);
        const formId = urlParams.get('form_id');
        document.getElementById('form_id').value = formId ? formId : 1; // Default form_id if not found

        // Handle form submission using AJAX
        $('#uploadForm').submit(function(e) {
            e.preventDefault(); // Prevent the default form submission

            var formData = new FormData(this); // Get form data
            $.ajax({
                url: 'uploadStudents.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#responseMessage').html(response); // Display the response from the server
                },
                error: function() {
                    $('#responseMessage').html('Error uploading data.'); // Error handling
                }
            });
        });
    </script>
</body>
</html>