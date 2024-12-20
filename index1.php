<?php
// Include database connection
include 'dbconn.php';

// Start session for user tracking
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

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
    $password = $_POST['password'];

    if (!empty($employee_id) && !empty($password)) {
        $query = "SELECT * FROM faculty WHERE employee_id = ?";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            $stmt->bind_param("s", $employee_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $faculty = $result->fetch_assoc();

                if (password_verify($password, $faculty['password'])) {
                    session_start();
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
            $error_message = 'Database query error: ' . $conn->error;
        }
    } else {
        $error_message = 'Please enter both Employee ID and Password.';
    }
}



// Handle Password Reset Logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset_password'])) {
    session_start(); // Ensure session is active

    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $otp = $_POST['otp'];

    header('Content-Type: application/json'); // Ensure correct JSON header

    if (isset($_SESSION['email_otp']) && $_SESSION['email_otp'] == $otp) {
        if ($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $email = $_SESSION['email_for_reset'];

            // Update password in the database
            $query = "UPDATE faculty SET password = ? WHERE email = ?";
            $stmt = $conn->prepare($query);

            if ($stmt) {
                $stmt->bind_param("ss", $hashed_password, $email);
                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Password reset successfully.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to update the password.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Passwords do not match.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid OTP.']);
    }
    exit;
}




// Handle OTP Send Logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_otp'])) {
    $email = $_POST['email'];
    // Check if email exists
    $Exists_Query = "SELECT email FROM faculty WHERE email = ?";
    $stmt = $conn->prepare($Exists_Query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Generate OTP and save in session
        $otp = rand(1000, 9999);
        $_SESSION['email_otp'] = $otp;
        $_SESSION['email_for_reset'] = $email;

        // Send Email (No changes)
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'akashlucky2232@gmail.com';
            $mail->Password = 'xnov lfbw inkv ckjd';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('akashlucky2232@gmail.com', 'MITS Faculty System');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset OTP';
            $mail->Body = 'Your OTP for resetting your password is ' . $otp;

            $mail->send();
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'OTP sent successfully.']);
            exit;
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Error: ' . $mail->ErrorInfo]);
            exit;
        }
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Email not found.']);
        exit;
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MITS | Faculty Login</title>
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
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
            justify-content: center;
            align-items: center;
            margin-bottom: 0px;
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
        <form id="otpForm" method="POST" action="">
        <div class="input-box">
            <input type="text" name="reset_employee_id" placeholder="Enter Employee ID" required>
        </div>
            <div class="input-box">
                <input type="email" id="email" name="email" placeholder="Enter your Email" required>
            </div>
            <button type="submit" id="sendOtpBtn" name="send_otp" class="btn">Send OTP</button>
        </form>
        <div id="otp-section" style="display:none; margin-top:1.5vh;">
            <form id="resetPasswordForm" method="POST" action="">
                <div class="input-box">
                    <input type="text" name="otp" placeholder="Enter OTP" required>
                </div>
                <div class="input-box">
                    <input type="password" name="new_password" placeholder="Enter New Password" required>
                </div>
                <div class="input-box">
                    <input type="password" name="confirm_password" placeholder="Re-enter New Password" required>
                </div>
                <button type="submit" name="reset_password" class="btn">Reset Password</button>
            </form>
        </div>
        <button type="button" onclick="closePopup()" class="btn" style="background-color:rgb(222, 56, 56);">Cancel</button>
    </div>


    <script>
        

        // Handle Reset Password Submission via AJAX
        document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent default form submission

            const formData = new FormData(this);
            formData.append('reset_password', true);

            fetch('index1.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    
                    closePopup();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                
            });
        });

        function openPopup() {
        document.getElementById('popupForm').style.display = 'block';
        }

        function closePopup() {
        document.getElementById('popupForm').style.display = 'none';
        }


    </script>
    <script>
    document.getElementById('otpForm').addEventListener('submit', function (e) {
    e.preventDefault(); // Prevent default form submission

    const email = document.getElementById('email').value;
    const sendOtpBtn = document.getElementById('sendOtpBtn');
    sendOtpBtn.disabled = true;
    sendOtpBtn.innerText = 'Sending...';

    const formData = new FormData();
    formData.append('send_otp', true);
    formData.append('email', email);

    fetch('index1.php', {
        method: 'POST',
        body: formData,
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('HTTP error! Status: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message,
                    timer: 3000,
                    showConfirmButton: false,
                });
                document.getElementById('otp-section').style.display = 'block';
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: data.message,
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'An error occurred. Please try again later.',
            });
            console.error('Error:', error);
        })
        .finally(() => {
            sendOtpBtn.disabled = false;
            sendOtpBtn.innerText = 'Send OTP';
        });
});



    // Handle Reset Password Submission via AJAX
   document.getElementById('resetPasswordForm').addEventListener('submit', function (e) {
    e.preventDefault(); // Prevent default form submission

    const formData = new FormData(this);
    formData.append('reset_password', true);

    fetch('index1.php', {
        method: 'POST',
        body: formData,
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error(HTTP error! Status: ${response.status});
            }
            return response.json(); // Parse JSON response
        })
        .then((data) => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message,
                    timer: 3000,
                    showConfirmButton: false,
                });
                closePopup();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: data.message,
                });
            }
        })
        .catch((error) => {
            console.error('Error:', error); // Log detailed error
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'An unexpected error occurred. Please try again.',
            });
        });
});


    function openPopup() {
        document.getElementById('popupForm').style.display = 'block';
    }

    function closePopup() {
        document.getElementById('popupForm').style.display = 'none';
    }

    // Handle invalid login
    <?php if (!empty($error_message)) : ?>
        Swal.fire({
            icon: 'error',
            title: 'Login Failed!',
            text: '<?php echo $error_message; ?>',
        });
    <?php endif; ?>
</script>

</body>
</html>