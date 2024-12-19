<!DOCTYPE html>
<html>
<head>
    <title>Home | Resources Information</title>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
        }
        .buttons button {
            width: 120px;
            height: 40px;
            border: none;
            border-radius: 5px;
            background-color: #C0C0C0;
            color: #fff;
            cursor: pointer;
            font-size: 14px;
            font-family: Poppins;
        }
        .buttons button:hover {
            background-color: #0056b3;
        }        

        .table-container {
            margin: 20px auto;
            width: 70%;
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
            font-family: Poppins;
        }
        .add-button {
            margin: 10px 0;
            padding: 8px 16px;
            font-size: 14px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-family: Poppins;
        }
        .add-button:hover {
            background-color: #0056b3;
        }
        
        /* Styles for the Remarks input fields */
        input[type="text"] {
            border: none; /* Remove the border */
            background-color: transparent; /* Make background transparent */
            outline: none; /* Remove focus outline */
            padding: 5px; /* Optional: Add padding for better spacing */
            
        }

        /* Add focus styling for better user experience */
        input[type="text"]:focus {
            border-bottom: 1px solid #ccc; /* Add a subtle underline on focus */
            background-color: transparent;
        }



    </style>
</head>
    

    <body>

    <?php include('faculty_sidebar.php'); ?>

    <div class="content">
        

        <div style="text-align: center; ">
            <img src="Mits_logo_24-removebg-preview.png" alt="Table Image" height="200" width="800">
        </div>
        <div class="heading-section">
            <label><center>Subject wise Faculty Approval Information</center></label>
        </div>
        <form>
            <div class="form-row">
                <div style="margin-left: 175px;">
                    <label>Year & Semester :-</label>
                    <input type="text" id="yearSemester" name="yearSemester" style="width: 290px;" readonly>
                </div>

                <div>
                    <label>Section :-</label>
                    <input type="text" id="section" name="section" style="width: 290px;" readonly>
                </div>
            </div>
            <label style="width: 10%; font-family: Poppins; color: purple; font-size: 20px; margin-left: 175px;">Students List of Subject</label>
            
            <div class="table-container">

            <table border="1" style="width: 110%; border-collapse: collapse; font-family: Poppins;">
            <thead>
                <tr>
                    <th style="color: purple; width: 10px;">S.No</th>
                    <th style="color: purple; width: 250px;">Student Name</th>
                    <th style="text-align: center; color: purple; width: 100px;">Assignment-1<input type="checkbox" id="selectAllSubject1" style="margin-left: 5px;" onclick="toggleCheckboxes('subject1')"></th>
                    <th style="text-align: center; color: purple; width: 100px;">Assignment-2<input type="checkbox" id="selectAllSubject2" style="margin-left: 5px;" onclick="toggleCheckboxes('subject2')"></th>
                    <th style="color: purple; width: 250px;">Remarks</th>
                </tr>
            </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Student One</td>
                        <td style="text-align: center; vertical-align: middle;">
                            <input type="checkbox" class="subject1" style="width: 20px; height: 20px;" checked>
                        </td>
                        <td style="text-align: center; vertical-align: middle;">
                            <input type="checkbox" class="subject2" style="width: 20px; height: 20px;" checked>
                        </td>
                        <td><input type="text" placeholder="Remarks" style="width: 100%; height: 30px; font-family: Poppins;"></td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Student Two</td>
                        <td style="text-align: center; vertical-align: middle;">
                            <input type="checkbox" class="subject1" style="width: 20px; height: 20px;" checked>
                        </td>
                        <td style="text-align: center; vertical-align: middle;">
                            <input type="checkbox" class="subject2" style="width: 20px; height: 20px;" checked>
                        </td>
                        <td><input type="text" placeholder="Remarks" style="width: 100%; height: 30px; font-family: Poppins;"></td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Student Three</td>
                        <td style="text-align: center; vertical-align: middle;">
                            <input type="checkbox" class="subject1" style="width: 20px; height: 20px;" checked>
                        </td>
                        <td style="text-align: center; vertical-align: middle;">
                            <input type="checkbox" class="subject2" style="width: 20px; height: 20px;" checked>
                        </td>
                        <td><input type="text" placeholder="Remarks" style="width: 100%; height: 30px; font-family: Poppins;"></td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>Student Four</td>
                        <td style="text-align: center; vertical-align: middle;">
                            <input type="checkbox" class="subject1" style="width: 20px; height: 20px;" checked>
                        </td>
                        <td style="text-align: center; vertical-align: middle;">
                            <input type="checkbox" class="subject2" style="width: 20px; height: 20px;" checked>
                        </td>
                        <td><input type="text" placeholder="Remarks" style="width: 100%; height: 30px; font-family: Poppins;"></td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td>Student Five</td>
                        <td style="text-align: center; vertical-align: middle;">
                            <input type="checkbox" class="subject1" style="width: 20px; height: 20px;" checked>
                        </td>
                        <td style="text-align: center; vertical-align: middle;">
                            <input type="checkbox" class="subject2" style="width: 20px; height: 20px;" checked>
                        </td>
                        <td><input type="text" placeholder="Remarks" style="width: 100%; height: 30px; font-family: Poppins;"></td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td>Student Six</td>
                        <td style="text-align: center; vertical-align: middle;">
                            <input type="checkbox" class="subject1" style="width: 20px; height: 20px;" checked>
                        </td>
                        <td style="text-align: center; vertical-align: middle;">
                            <input type="checkbox" class="subject2" style="width: 20px; height: 20px;" checked>
                        </td>
                        <td><input type="text" placeholder="Remarks" style="width: 100%; height: 30px; font-family: Poppins;"></td>
                    </tr>
                </tbody>
            </table>
            </div>

            
    </div>

<script>
    function toggleCheckboxes(className) {
        const checkboxes = document.querySelectorAll('.' + className);
        const selectAll = document.getElementById('selectAll' + className.charAt(0).toUpperCase() + className.slice(1));
        checkboxes.forEach((checkbox) => {
            checkbox.checked = selectAll.checked;
        });
    }
</script>

</body>
</html>
