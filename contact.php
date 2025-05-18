<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/contact/vendor/PHPMailer/PHPMailer.php';
require __DIR__ . '/contact/vendor/PHPMailer/SMTP.php';
require __DIR__ . '/contact/vendor/PHPMailer/Exception.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method.';
    echo json_encode($response);
    exit;
}

$name    = isset($_POST['name'])    ? strip_tags(trim($_POST['name']))    : '';
$email   = isset($_POST['email'])   ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
$message = isset($_POST['message']) ? strip_tags(trim($_POST['message'])) : '';

if (empty($name) || empty($email) || empty($message)) {
    $response['message'] = 'All fields are required.';
    echo json_encode($response);
    exit;
}

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = 'mail.mcgrewmedia.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'russ@mcgrewmedia.com';
    $mail->Password   = 'Deedni!!72m@!l1'; // Consider moving this to a secure config file
	$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
	$mail->Port       = 465;

    $mail->setFrom('russ@mcgrewmedia.com', 'McGrew Media Contact');
    $mail->addAddress('russ@mcgrewmedia.com');
    $mail->addReplyTo($email, $name);

    $mail->isHTML(true);
    $mail->Subject = "New Contact Form Message from $name";
    $mail->Body    = "<strong>Name:</strong> $name<br><strong>Email:</strong> $email<br><strong>Message:</strong><br>" . nl2br($message);

    $mail->send();
		header("Location: /?sent=1");
		exit;

    $response['success'] = true;
    $response['message'] = 'Message sent successfully.';
} catch (Exception $e) {
    $response['message'] = 'Mailer Error: ' . $mail->ErrorInfo;
}

//echo json_encode($response);
