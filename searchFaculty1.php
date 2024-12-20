<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>MITS_Staff_List</title>
    <style>
        body {
            font-family: Poppins;
        }
        .container {
            font-family: Poppins;
            width: 55%;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
        }
        .form-group {
            margin-bottom: 15px;
            font-family: Poppins;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-family: Poppins;
            font-size: 14px;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            font-family: Poppins;
        }
        .submit-btn, .clear-btn {
            background-color: #555;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            font-family: Poppins;
            height: 40px;
            width: 100px;
        }
        .submit-btn:hover, .clear-btn:hover {
            background-color: #E1D9D1;
        }
        .button-row {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        .image-row {
            text-align: center;
        }
        .image-row img {
            max-width: 100%;
            height: 200px;
        }
        #myBtn {
            font-family: Poppins;
            display: none; 
            position: fixed; 
            bottom: 20px; 
            right: 200px; 
            z-index: 99; 
            border: none; 
            outline: none; 
            background-color: #555; 
            color: white; 
            cursor: pointer; 
            padding: 15px; 
            border-radius: 10px; 
        }
        #myBtn:hover {
            background-color: #BCC6CC; 
        }
        .department-table {
            width: 100%;
            font-family: Poppins, sans-serif;
            border-collapse: collapse;
        }
        .department-table th, .department-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 2px solid #ddd;
        }
        .department-table th {
            background-color: #f2f2f2;
            color: purple;
            /* width: 180px; */
        }
        .department-table a {
            text-decoration: none;
            color: purple;
        }
    </style>
</head>
<body>
<div class="container">
    <table style="width: 100%;">
        <tr>
            <td colspan="4" class="image-row">
                <img src="Mits_logo_24.jpg" alt="Table Image" height="550" width="800">
            </td>
        </tr>
    </table>
    <label style="font-family: Poppins; color: purple; font-weight: bold; text-decoration: underline; display: block; text-align: center; font-size: 24px;">
        Staff Information
    </label>
    <br>
    <!-- <form action="searchfaculty.php" method="post" id="ffc"> -->
    <form method="post">
        <div class="search-container" style="margin-bottom: 20px;">
            <label for="search">Staff (Employee ID or Name)</label>
            <!-- <input type="text" id="search" name="search" autocomplete="on"> -->
            <input type="text" id="search" name="search" autocomplete="on" placeholder="Enter Employee ID or Name" style="padding: 5px; width: 380px; font-family: Poppins;">
            <input type="submit" class="submit-btn" value="Submit">
            <button type="button" class="clear-btn" onclick="clearTable()">Clear</button>
        </div>
    </form>
    <div class="department-table">
        <table id="employeeTable">
            <thead>
                <tr>
                    <th style="width:150px">Employee ID</th>
                    <th style="width:220px">Name</th>
                    <th style="width:160px">Department</th>
                    <th style="width:180px">E-Mail</th>
                    <th style="width:160px">Cell Number</th>
                </tr>
            </thead>
            <tbody id="tableBody">                <!-- Table rows will be inserted here dynamically -->
            </tbody>
        </table>
    </div>


<script>
$(document).ready(function () {
    $("#search").autocomplete({
        source: "search.php", 
        minLength: 2,
        select: function (event, ui) {
            const tableBody = $("#tableBody");
            tableBody.empty(); 
            const row = `<tr>
                <td>${ui.item.id}</td>
                <td>${ui.item.label}</td>
                <td>${ui.item.department}</td>
                <td>${ui.item.email}</td>
                <td>${ui.item.phone}</td>
            </tr>`;
            tableBody.append(row);
        }
    });
});
</script>


<script>
    function clearTable() {
        document.getElementById('search').value = '';
        document.getElementById('tableBody').innerHTML = '';
    }
</script>

</div>
</body>
</html>
