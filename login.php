<?php
// Include database connection
include 'dbconn.php';

// Start session for user tracking
session_start();

// Check if user is already logged in, then redirect to the dashboard
if (isset($_SESSION['department_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error_message = ''; // Initialize error message variable

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form input values
    $department = mysqli_real_escape_string($conn, $_POST['department']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    if (!empty($department) && !empty($password)) {
        // Hash the entered password using MD5
        $hashed_password = md5($password);

        // Join `users` and `departments` tables to validate login
        $query = "
            SELECT users.*, departments.department_id
            FROM users
            INNER JOIN departments ON users.department_id = departments.department_id
            WHERE departments.department_name = '$department' AND users.password = '$hashed_password'
        ";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            // Fetch the user's data
            $user = mysqli_fetch_assoc($result);

            // Start a session and store department_id
            $_SESSION['department_id'] = $user['department_id'];

            // Redirect to the dashboard
            header('Location: dashboard.php');
            exit;
        } else {
            $error_message = 'Incorrect department or password.'; // Set error message
        }
    } else {
        $error_message = 'Please enter both department and password.'; // Set error message for missing fields
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MITS | Login</title>
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">

    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="styles.css">
    
    <!-- Include SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .btn {
            width: 100%;
            height: 50px;
            padding: 10px;
            border: none;
            border-radius: 40px;
            background:#9ACD32;
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
        .ex1 {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 0px;

            color: #d45454;
        }
        .ex1 h1 {
            margin-bottom: -10px;
        }
    </style>
</head>
<body>
    <div class="ex">
        <img src="Mits_logo_24-removebg-preview.png" alt="MITS Logo" height="200" width="800">
    </div>
    <div class="ex1"><h1>[ -- Internal Access -- ]</h1></div>
    <div class="wrapper">
        <h1>HOD Login</h1>
        <form method="POST" action="login.php">
            <div class="input-box">
                <input type="text" style="font-family: Poppins;" name="department" placeholder="Department" required>
                <i class='bx bxs-user'></i>
            </div>
            <div class="input-box">
                <input type="password" style="font-family: Poppins;" name="password" placeholder="Password" required>
                <i class='bx bxs-lock-alt'></i>
            </div>
            
            

            <button type="submit" style="font-family: Poppins;" class="btn">Login</button>
            <div class="reset-link" style="margin-top:20px;">
                <a href="index.php" style="color:#d45454;">← Back to Faculty Login</a>
            </div>
        </form>
    </div>

    <script>
        // Show SweetAlert based on error message
        <?php if (!empty($error_message)) { ?>
            Swal.fire({
                icon: 'error',
                title: 'Login Failed',
                text: '<?php echo $error_message; ?>'
            });
        <?php } ?>
    </script>
</body>
</html>
