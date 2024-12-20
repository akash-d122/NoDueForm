<?php
session_start();
include 'dbconn.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset_password'])) {
    $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    // Get employee_id from session
    $employee_id = $_SESSION['employee_id'] ?? null;

    if (!empty($employee_id) && !empty($new_password) && !empty($confirm_password)) {
        if ($new_password === $confirm_password) {
            // Hash the new password
            $new_hashed_password = password_hash($new_password,PASSWORD_DEFAULT);

            // Update the password in the database
            $update_query = "UPDATE faculty SET password = '$new_hashed_password' WHERE employee_id = '$employee_id'";
            if (mysqli_query($conn, $update_query)) {
                $success_message = 'Password reset successfully. You can now log in.';
            } else {
                $error_message = 'Error resetting password. Please try again.';
            }
        } else {
            $error_message = 'New passwords do not match.';
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
    <title>Reset Faculty Password</title>
    <link rel="stylesheet" href="styles.css">
    <style> 
        body {
            min-height: 100vh;
            background: linear-gradient(to bottom right, #c3e6cb, #ffffff);
            background-size: cover;
            background-position: center;
            font-family: "Poppins", Georgia;
        }
        .reset-password-form {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 35vw;
            height: 30vh;
            margin: auto;
            margin-top: 50px;
        }
        .reset-password-form h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .input-box {
            margin-bottom: 15px;
        }
        .input-box input {
            width: 95%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .btn {
            width: 98%;
            padding: 10px;
            background: #28a745;
            border: none;
            color: #fff;
            border-radius: 5px;
            margin-top: 30px;
            cursor: pointer;
        }
        .btn:hover {
            background: #218838;
        }
        .success {
            color: green;
            text-align: center;
        }
        .error {
            color: red;
            text-align: center;
        }
        .ex {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 20px;
        color: #d45454;
        margin-left: 15%;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <?php include('faculty_sidebar.php'); ?>
    <div class="ex">
        <img src="Mits_logo_24-removebg-preview.png" alt="MITS Logo" height="200" width="800">
    </div>
    <div style="margin-left:17vw;display:flex;justify-content:center;">
        <div class="reset-password-form" >
            <h2>Reset Faculty Password</h2>
            <?php if (isset($success_message)) { echo "<p class='success'>$success_message</p>"; } ?>
            <?php if (isset($error_message)) { echo "<p class='error'>$error_message</p>"; } ?>
            <form method="POST" action="">
                <div class="input-box">
                    <input type="password" name="new_password" placeholder="Enter New Password" required>
                </div>
                <div class="input-box">
                    <input type="password" name="confirm_password" placeholder="Re-enter New Password" required>
                </div>
                <button type="submit" name="reset_password" class="btn">Reset Password</button>
            </form>
        </div>
    </div>
</body>
</html>