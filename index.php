<?php
// Include database connection
// Include database connection
include 'dbconn.php';

// Start session for user tracking
session_start();

// Check if user is already logged in, then redirect to the faculty dashboard
if (isset($_SESSION['employee_id'])) {
    header('Location: faculty_dashboard.php');
    exit;
}

$error_message = '';
$success_message = '';

// Handle Login Logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $employee_id = mysqli_real_escape_string($conn, $_POST['employee_id']);
    $password = $_POST['password']; // No need to hash here

    if (!empty($employee_id) && !empty($password)) {
        // Retrieve the stored hashed password from the database
        $query = "SELECT * FROM faculty WHERE employee_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $employee_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $faculty = $result->fetch_assoc();

            // Verify the entered password against the stored hash
            if (password_verify($password, $faculty['password'])) {
                $_SESSION['employee_id'] = $faculty['employee_id'];
                header('Location: faculty_dashboard.php');
                exit;
            } else {
                $error_message = 'Invalid Employee ID or Password.';
            }
        } else {
            $error_message = 'Invalid Employee ID or Password.';
        }
    } else {
        $error_message = 'Please enter both Employee ID and Password.';
    }
}


// Handle Password Reset Logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset_password'])) {
    $employee_id = mysqli_real_escape_string($conn, $_POST['reset_employee_id']);
    $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    if (!empty($employee_id) && !empty($new_password) && !empty($confirm_password)) {
        if ($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            

            // Update the password in the faculty table
            $query = "UPDATE faculty SET password = '$hashed_password' WHERE employee_id = '$employee_id'";
            if (mysqli_query($conn, $query)) {
                $success_message = 'Password reset successfully. You can now log in.';
            } else {
                $error_message = 'Error resetting password. Please try again.';
            }
        } else {
            $error_message = 'Passwords do not match.';
        }
    } else {
        $error_message = 'All fields are required.';
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MITS | Faculty Login</title>
    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* Same CSS as before */
        .btn {
            width: 100%;
            height: 50px;
            padding: 10px;
            border: none;
            border-radius: 40px;
            background: #9ACD32;
            color: #333;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }
        .btn:hover {
            background: #7A9B2E;
            color: white;
        }
        .error-message {
            color: red;
            font-size: 14px;
            margin: 10px 0;
        }
        .success-message {
            color: green;
            font-size: 14px;
            margin: 10px 0;
        }
        .ex1 {
            display: flex;
            font-size: 10px;
            justify-content: center;
            align-items: center;
            margin-bottom: -10px;
            color: #d45454;
        }
        .ex1 h1 {
            margin-bottom: -10px;
        }
        .input-box input[name="employee_id"] {
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="ex">
        <img src="Mits_logo_24-removebg-preview.png" alt="MITS Logo" height="200" width="800">
    </div>
    <div class="ex1"><h1>[ -- Internal Access -- ]</h1></div>

    <div class="wrapper">
        <h1>Faculty Login</h1>
        <form method="POST" action="">
            <div class="input-box">
                <input type="text" style="font-family: Poppins;" name="employee_id" placeholder="Employee ID" required>
                <i class='bx bxs-user'></i>
            </div>
            <div class="input-box">
                <input type="password" style="font-family: Poppins;" name="password" placeholder="Password" required>
                <i class='bx bxs-lock-alt'></i>
            </div>

    

            <button type="submit" style="font-family: Poppins;" name="login" class="btn">Login</button>
            <div class="reset-link">
                <a href="#" onclick="openPopup()">Forgot Password?</a>
            </div>
            <div class="reset-link" style="margin-top:20px;">
                <a href="login.php" style="color:#d45454; font-family: Poppins;">HOD Login</a>
            </div>
        </form>
    </div>

    <!-- Reset Password Popup -->
    <div id="popupForm" class="popup" style="display:none;">
        <h2>Reset Password</h2>
        <form method="POST" action="">
            <div class="input-box">
                <input type="text" name="reset_employee_id" placeholder="Enter your Employee ID" required>
            </div>
            <div class="input-box">
                <input type="password" name="new_password" placeholder="Enter New Password" required>
            </div>
            <div class="input-box">
                <input type="password" name="confirm_password" placeholder="Re-enter New Password" required>
            </div>
            <button type="submit" name="reset_password" class="btn">Reset Password</button>
            
        </form>
        <button onclick="closePopup()" class="btn">Close</button>
    </div>

    <script>
        function openPopup() {
            document.getElementById('popupForm').style.display = 'block';
        }
        function closePopup() {
            document.getElementById('popupForm').style.display = 'none';
        }
    </script>

<?php if (!empty($error_message)) { ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '<?php echo $error_message; ?>',
            confirmButtonText: 'OK'
        });
    </script>
    <?php } ?>

    <?php if (!empty($success_message)) { ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: '<?php echo $success_message; ?>',
            confirmButtonText: 'OK'
        });
    </script>
    <?php } ?>
</body>
</html>
