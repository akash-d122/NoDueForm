<!DOCTYPE html>
<html>
<head>
    <title>MITS | NoDueForm</title>
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
            margin-top: 60px;
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
            border: 1px solid #ddd;
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

    </style>
</head>
<body>
    <div class="sidebar">
        
    </div>

    <body>
    <div class="content">
        <div style="text-align: center;margin-top:-20px;">
            <img src="Mits_logo_24-removebg-preview.png" alt="Table Image" height="200" width="800">
        </div>
        <div class="heading-section"style="align-items:center;margin-top:2">
            <label>Mentoring & Other Information</label>
        </div>

        
        <form>
            

            <label style="width: 10%; font-family: Poppins; color: purple; font-size: 20px; margin-left: 175px;">Mentoring Information</label>
    <div class="table-container" style="margin-left: 80px;"">
    <table border="1" style="width: 120%; border-collapse: collapse; font-family: Poppins;">
        <thead>
            <tr>
                <th style="color: purple; width: 10px;">S.No</th>
                <th style="color: purple; width: 250px;">Roll Number</th>
                <th style="text-align: center; color: purple; width: 100px;">
                    Student Achievements 
                    <input type="checkbox" id="selectAllYes" style="margin-left: 5px;" onclick="toggleCheckboxes('Yes')">
                </th>
                <th style="text-align: center; color: purple; width: 100px;">
                    NASSCOM Certification 
                    <input type="checkbox" id="selectAllNo" style="margin-left: 5px;" onclick="toggleCheckboxes('No')">
                </th>
                <th style="text-align: center; color: purple; width: 100px;">
                    Course Exit Survey 
                    <input type="checkbox" id="selectAllNA" style="margin-left: 5px;" onclick="toggleCheckboxes('NA')">
                </th>
                <th style="text-align: center; color: purple; width: 100px;">AICTE 360 Feedback
                    <input type="checkbox" id="selectAllNA" style="margin-left: 5px;" onclick="toggleCheckboxes('NA')">
                </th>
                <th style="text-align: center; color: purple; width: 100px;">Mentor Mentee Meeting
                    <input type="checkbox" id="selectAllNA" style="margin-left: 5px;" onclick="toggleCheckboxes('NA')">
                </th>
                <th style="text-align: center; color: purple; width: 100px;">NPTEL Certificate
                    <input type="checkbox" id="selectAllNA" style="margin-left: 5px;" onclick="toggleCheckboxes('NA')">
                </th>
                <th style="text-align: center; color: purple; width: 100px;">Soft Skills Training
                    <input type="checkbox" id="selectAllNA" style="margin-left: 5px;" onclick="toggleCheckboxes('NA')">
                </th>
                <th style="text-align: center; color: purple; width: 100px;">Skill Oriented Course
                    <input type="checkbox" id="selectAllNA" style="margin-left: 5px;" onclick="toggleCheckboxes('NA')">
                </th>
                <th style="text-align: center; color: purple; width: 100px;">Others
                    <input type="checkbox" id="selectAllNA" style="margin-left: 5px;" onclick="toggleCheckboxes('NA')">
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>23691A0XXX</td>
                <td style="text-align: center;">
                    <input type="checkbox" data-value="Yes" style="width: 15px; height: 15px; margin-left: 30px;" checked>
                    <label style="font-size: 14px;">Yes</label>
                    <input type="checkbox" data-value="No" style="width: 15px; height: 15px; margin-left: 30px;">
                    <label style="font-size: 14px;">No</label>
                    <input type="checkbox" data-value="NA" style="width: 15px; height: 15px; margin-left: 30px;">
                    <label style="font-size: 14px;">NA</label>
                </td>
                <td style="text-align: center;">
                    <input type="checkbox" data-value="Yes" style="width: 15px; height: 15px; margin-left: 30px;" checked>
                    <label style="font-size: 14px;">Yes</label>
                    <input type="checkbox" data-value="No" style="width: 15px; height: 15px; margin-left: 30px;">
                    <label style="font-size: 14px;">No</label>
                    <input type="checkbox" data-value="NA" style="width: 15px; height: 15px; margin-left: 30px;">
                    <label style="font-size: 14px;">NA</label>
                </td>
                <td style="text-align: center;">
                    <input type="checkbox" data-value="Yes" style="width: 15px; height: 15px; margin-left: 30px;" checked>
                    <label style="font-size: 14px;">Yes</label>
                    <input type="checkbox" data-value="No" style="width: 15px; height: 15px; margin-left: 30px;">
                    <label style="font-size: 14px;">No</label>
                    <input type="checkbox" data-value="NA" style="width: 15px; height: 15px; margin-left: 30px;">
                    <label style="font-size: 14px;">NA</label>
                </td>
                <td style="text-align: center;">
                    <input type="checkbox" data-value="Feedback" style="width: 15px; height: 15px; margin-left: 30px;" checked>
                    <label style="font-size: 14px;">Yes</label>
                    <input type="checkbox" data-value="No" style="width: 15px; height: 15px; margin-left: 30px;">
                    <label style="font-size: 14px;">No</label>
                    <input type="checkbox" data-value="NA" style="width: 15px; height: 15px; margin-left: 30px;">
                    <label style="font-size: 14px;">NA</label>
                </td>
                <td style="text-align: center;">
                    <input type="checkbox" data-value="Meeting" style="width: 15px; height: 15px; margin-left: 30px;" checked>
                    <label style="font-size: 14px;">Yes</label>
                    <input type="checkbox" data-value="No" style="width: 15px; height: 15px; margin-left: 30px;">
                    <label style="font-size: 14px;">No</label>
                    <input type="checkbox" data-value="NA" style="width: 15px; height: 15px; margin-left: 30px;">
                    <label style="font-size: 14px;">NA</label>
                </td>
                <td style="text-align: center;">
                    <input type="checkbox" data-value="Yes" style="width: 15px; height: 15px; margin-left: 30px;" checked>
                    <label style="font-size: 14px;">Yes</label>
                    <input type="checkbox" data-value="No" style="width: 15px; height: 15px; margin-left: 30px;">
                    <label style="font-size: 14px;">No</label>
                    <input type="checkbox" data-value="NA" style="width: 15px; height: 15px; margin-left: 30px;">
                    <label style="font-size: 14px;">NA</label>
                </td>
                <td style="text-align: center;">
                    <input type="checkbox" data-value="Skills" style="width: 15px; height: 15px; margin-left: 30px;">
                    <label style="font-size: 14px;">Yes</label>
                    <input type="checkbox" data-value="No" style="width: 15px; height: 15px; margin-left: 30px;">
                    <label style="font-size: 14px;">No</label>
                    <input type="checkbox" data-value="NA" style="width: 15px; height: 15px; margin-left: 30px;">
                    <label style="font-size: 14px;">NA</label>
                </td>
                <td style="text-align: center;">
                    <input type="checkbox" data-value="Course" style="width: 15px; height: 15px; margin-left: 30px;">
                    <label style="font-size: 14px;">Yes</label>
                    <input type="checkbox" data-value="No" style="width: 15px; height: 15px; margin-left: 30px;">
                    <label style="font-size: 14px;">No</label>
                    <input type="checkbox" data-value="NA" style="width: 15px; height: 15px; margin-left: 30px;">
                    <label style="font-size: 14px;">NA</label>
                </td>
                <td style="text-align: center;">
                    <input type="checkbox" data-value="Others" style="width: 15px; height: 15px; margin-left: 30px;">
                    <label style="font-size: 14px;">Yes</label>
                    <input type="checkbox" data-value="No" style="width: 15px; height: 15px; margin-left: 30px;">
                    <label style="font-size: 14px;">No</label>
                    <input type="checkbox" data-value="NA" style="width: 15px; height: 15px; margin-left: 30px;">
                    <label style="font-size: 14px;">NA</label>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<div style="display: flex; align-items: center; gap: 20px; margin-left: 250px; margin-top: 20px;">
            <button type="button" style="font-family: Poppins; width:120px; height:40px; margin-left: 300px;" onclick="window.location.href='staffapp1.php';">Home</button>
            <button type="button" style="font-family: Poppins; width:120px; height:40px;" class="save-btn">Save</button>
            <button type="button" style="font-family: Poppins; width:120px; height:40px;" class="save-btn">Submit</button>
            <button type="button" style="font-family: Poppins; width:120px; height:40px;" class="logout-btn" onclick="window.location.href='logout.php';">Logout</button>
        </div>
        <!-- <tr>
          <class="footer-row" style="font-size: 14px; text-align: center; height: 24px; margin-left: 350px; margin-top:80px; background-color: #f1f1f1; color: #333; padding: 5px;">
                Developed & Hosted by MITS_InstituteDatabaseSystem@PAARC
        </tr> -->
    </tbody>
    </div>
    <script>
    function addRow() {
        const table = document.querySelector("table tbody");
        const rowCount = table.rows.length + 1;

        const newRow = document.createElement("tr");

        newRow.innerHTML = `
            <td>${rowCount}</td>
            <td><input type="text" placeholder="Enter Subject Name" style="width: 100%; height: 30px; font-family: Poppins;"></td>
            <td><input type="text" placeholder="Enter Faculty Name" style="width: 100%; height: 30px; font-family: Poppins;"></td>
            <td><button type="button" onclick="removeRow(this)" style="background-color: purple; color: white; font-family: Poppins; border: none; padding: 5px 10px; cursor: pointer;">Remove</button></td>
        `;

        table.appendChild(newRow);
        updateRowNumbers();
    }

    function removeRow(button) {
        const row = button.closest("tr");
        const table = row.parentNode;
        table.removeChild(row);
        updateRowNumbers();
    }

    function updateRowNumbers() {
        const rows = document.querySelectorAll("table tbody tr");
        rows.forEach((row, index) => {
            row.firstElementChild.textContent = index + 1;
        });
    }

    function toggleEdit(button) {
    const row = button.closest("tr"); 
    const inputField = row.querySelector("input"); 

    if (button.textContent === "Edit") {
        inputField.removeAttribute("readonly");
        inputField.focus(); 
        button.textContent = "Save"; 
    } else {
        inputField.setAttribute("readonly", true);
        button.textContent = "Edit"; 
    }
}


 function toggleCheckboxes(value) {
        const checkboxes = document.querySelectorAll(`input[data-value="${value}"]`);
        const selectAll = document.getElementById('selectAll' + value);

        checkboxes.forEach((checkbox) => {
            checkbox.checked = selectAll.checked;
        });
    }
    </script>
</body>
</html>