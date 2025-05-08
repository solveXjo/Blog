<?php

namespace App\Controllers;

use App\Core\Database;

require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;




class ContactController
{
    private $errors = [];
    private $errorMessage = '';
    private $successMessage = '';
    private $siteKey = '6LdAQycrAAAAAIZBSBgOG0Crw_l41tKp7j9xigEJ';
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    private function sanitizeInput($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    private function getRecaptchaResponse($recaptchaResponse)
    {
        $secretKey = '6LdAQycrAAAAABql1O8Im94AF2vuJz29p5Gezrqh';
        $errors = [];

        if (empty($recaptchaResponse)) {
            $errors[] = "Please complete the reCAPTCHA verification.";
        } else {
            $recaptchaURL = 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secretKey . '&response=' . $recaptchaResponse;
            $verify = json_decode(file_get_contents($recaptchaURL));

            if (!$verify->success) {
                $errors[] = "reCAPTCHA verification failed. Please try again.";
            }
        }
        return $errors;
    }

    public function handleContactForm()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $name = $this->sanitizeInput($_POST["name"] ?? '');
        $email = $this->sanitizeInput($_POST["email"] ?? '');
        $phone = $this->sanitizeInput($_POST["phone"] ?? '');
        $service = $this->sanitizeInput($_POST["subject"] ?? '');
        $message = $this->sanitizeInput($_POST["message"] ?? '');
        $recaptchaResponse = $this->sanitizeInput($_POST["g-recaptcha-response"] ?? '');


        $this->errors = [];
        if (empty($name)) $this->errors[] = "Name is required.";
        if (empty($email)) $this->errors[] = "Email is required.";
        if (empty($phone)) $this->errors[] = "Phone number is required.";
        if (empty($service)) $this->errors[] = "Please select a service.";
        if (empty($message)) $this->errors[] = "Message is required.";

        // Validate reCAPTCHA
        $recaptchaErrors = $this->getRecaptchaResponse($recaptchaResponse);
        $this->errors = array_merge($this->errors, $recaptchaErrors);

        if (!empty($this->errors)) {
            $this->errorMessage = "<div class='alert alert-danger'>" . implode('<br>', $this->errors) . "</div>";
            return false;
        }

        // Send email
        $toEmail = "fathii.alsadi@gmail.com";
        $emailSubject = "New contact form submission from $name";

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'sandbox.smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Username = 'd55b5020c57491';
            $mail->Password = '559530105d9cba';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom($email, $name);
            $mail->addAddress($toEmail);
            $mail->addReplyTo($email, $name);

            $mail->isHTML(true);
            $mail->Subject = $emailSubject;
            $mail->Body = "
                <h1>New message</h1>
                <p><strong>Name:</strong> $name</p>
                <p><strong>Email:</strong> $email</p>
                <p><strong>Phone:</strong> $phone</p>
                <p><strong>Service:</strong> $service</p>
                <p><strong>Message:</strong> $message</p>
                <p><strong>Time:</strong> " . date('Y-m-d H:i:s') . "</p>
            ";
            $mail->AltBody = "Name: $name\nEmail: $email\nPhone: $phone\nService: $service\nMessage: $message\nTime: " . date('Y-m-d H:i:s');

            if ($mail->send()) {
                $this->successMessage = "<div class='alert alert-success'>Your message has been sent successfully!</div>";
                return true;
            } else {
                throw new Exception('Message could not be sent. Mailer Error: ' . $mail->ErrorInfo);
            }
        } catch (Exception $e) {
            $this->errorMessage = "<div class='alert alert-danger'>Message could not be sent. Error: " . $e->getMessage() . "</div>";
            return false;
        }
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    public function getSuccessMessage()
    {
        return $this->successMessage;
    }
    public function getSiteKey()
    {
        return $this->siteKey;
    }
}
