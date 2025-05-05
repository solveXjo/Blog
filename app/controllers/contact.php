<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once   'vendor/autoload.php';

$errors = [];
$errorMessage = '';
$successMessage = '';
$siteKey = '6LdAQycrAAAAAIZBSBgOG0Crw_l41tKp7j9xigEJ';
$secretKey = '6LdAQycrAAAAABql1O8Im94AF2vuJz29p5Gezrqh';

function sanitizeInput($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = sanitizeInput($_POST["name"] ?? '');
    $email = sanitizeInput($_POST["email"] ?? '');
    $phone = sanitizeInput($_POST["phone"] ?? '');
    $service = sanitizeInput($_POST["subject"] ?? '');
    $message = sanitizeInput($_POST["message"] ?? '');
    $recaptchaResponse = sanitizeInput($_POST["g-recaptcha-response"] ?? '');

    if (empty($recaptchaResponse)) {
        $errors[] = "Please complete the reCAPTCHA verification.";
    } else {
        $recaptchaURL = 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secretKey . '&response=' . $recaptchaResponse;
        $verify = json_decode(file_get_contents($recaptchaURL));

        if (!$verify->success) {
            $errors[] = "reCAPTCHA verification failed. Please try again.";
        }
    }

    if (empty($name)) $errors[] = "Name is required.";
    if (empty($email)) $errors[] = "Email is required.";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format.";
    if (empty($phone)) $errors[] = "Phone number is required.";
    if (empty($service)) $errors[] = "Please select a service.";
    if (empty($message)) $errors[] = "Message is required.";

    if (!empty($errors)) {
        $errorMessage = "<div class='alert alert-danger'>" . implode('<br>', $errors) . "</div>";
    } else {
        $toEmail = "fathii.alsadi@gmail.com";
        $emailSubject = "New contact form submission from $name";

        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'sandbox.smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Username = 'd55b5020c57491';
            $mail->Password = '559530105d9cba';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom($email, $name);
            $mail->addAddress($toEmail);
            $mail->addReplyTo($email, $name);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $emailSubject;
            $mail->Body = "
                <h1>New message</h1>
                <p><strong>Name:</strong> $name</p>
                <p><strong>Email:</strong> $email</p>
                <p><strong>Phone:</strong> $phone</p>
                <p><strong>Service:</strong> $service</p>
                <p><strong>Message:</strong> $message</p>
            ";
            $mail->AltBody = "Name: $name\nEmail: $email\nPhone: $phone\nService: $service\nMessage: $message";

            if ($mail->send()) {
                $successMessage = "<div class='alert alert-success'>Your message has been sent successfully!</div>";
            } else {
                throw new Exception('Message could not be sent. Mailer Error: ' . $mail->ErrorInfo);
            }
        } catch (Exception $e) {
            $errorMessage = "<div class='alert alert-danger'>Message could not be sent. Error: " . $e->getMessage() . "</div>";
        }
    }
}
