<?php
require_once('dbconn.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Allow CORS if needed
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Validate POST data
$postData = json_decode(file_get_contents("php://input"), true);
if (empty($postData['email']) || empty($postData['name']) || empty($postData['password'])) {
    echo json_encode(['status' => 'error', 'message' => 'Incomplete POST data']);
    exit;
}

$email = $postData['email'];
$name = $postData['name'];
$password = $postData['password'];

// Send email
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'akashlucky2232@gmail.com'; // Replace with your Gmail
    $mail->Password = 'xnov lfbw inkv ckjd';   // Replace with your Gmail App Password
    // $mail->Username = 'mitsnodueform@gmail.com'; // Replace with your Gmail
    // $mail->Password = 'lkqa clch gdfo bciu';   // Replace with your Gmail App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('mitsnodueform@gmail.com', 'Student No Due Form -- MITS'); // Replace with your Gmail
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = 'Student No Due Form Online Approval Application -- MITS';
    $mail->Body = "
            <h2>Welcome Sir / Madam $name, </h2>
            <p>Your Faculty Account (Institute Employee ID) has been successfully linked to the Students' No Due Form. Below are your login credentials:</p>  
            <ul>
                <li><strong>Email:</strong> $email</li>
                <li><strong>Password:</strong> $password</li>
            </ul>            
            <p>Copy and Paste this URL into your browser to Login: <br> 
                <strong>http://117.250.201.144/nodueform/index.php</strong>
            </p>
            <p>
            @@@ This is Automatically Generated E-Mail. Replies to this E-Mail are UNDELIVERABLE and will not reach -- MITS@PAARC @@@
            </p>
        ";

    $mail->send();

    echo json_encode(['status' => 'success']);
} catch (Exception $e) {
    error_log("Email Error: " . $mail->ErrorInfo);
    echo json_encode(['status' => 'error', 'message' => $mail->ErrorInfo]);
}
exit;
