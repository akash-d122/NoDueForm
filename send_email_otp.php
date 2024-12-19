<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if (isset($_POST['email'])) {
    $email = $_POST['email'];

    // Include database connection
    include 'dbconn.php';

    // Check if email exists in the faculty table
    $Exists_Query = "SELECT email FROM faculty WHERE email = '$email'";
    $exits_result = mysqli_query($conn, $Exists_Query);
    $Total = mysqli_num_rows($exits_result);

    if ($Total > 0) {
        // Generate OTP and store it in the session
        $otp = rand(1000, 9999);
        $_SESSION['email_otp'] = $otp;
        $_SESSION['email_for_reset'] = $email; // Store email for later use in password reset

        // Send OTP to the email address
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'akashlucky2232@gmail.com'; // Your email
            $mail->Password = 'xnov lfbw inkv ckjd';  // Your email password or app-specific password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('akashlucky2232@gmail.com', 'MITS Faculty System');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset OTP';
            $mail->Body = 'Your OTP for resetting your password is ' . $otp;

            // Send the email
            $mail->send();
            echo json_encode(['status' => 'otp_sent']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $mail->ErrorInfo]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Email not found.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Email is required']);
}
?>
