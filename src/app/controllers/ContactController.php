<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Queue\Queue;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

// This is the correct namespace
class ContactController extends BaseController
{
    private $errors = [];
    private $errorMessage = '';
    private $successMessage = '';
    private $siteKey = '6LdAQycrAAAAAIZBSBgOG0Crw_l41tKp7j9xigEJ';
    private $queue;

    public function __construct()
    {
        parent::__construct();
        $this->queue = new Queue('storage/queue');
    }

    // #[Route('/contact', 'GET')]
    public function show()
    {
        $this->handleContactForm();
        echo $this->view->renderWithLayout('contact.view.php', 'layouts/main.php', [
            'title' => 'Contact - Altibbi',
            'siteKey' => $this->siteKey,
            'successMessage' => $this->successMessage,
            'errorMessage' => $this->errorMessage,
            'contactController' => $this  // Pass $this instead of new instance
        ]);
    }

    public function handle()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /contact');
            exit();
        }

        $result = $this->processForm();

        error_log("Form processed. Result: " . ($result ? 'true' : 'false'));

        $this->show();
    }

    private function processForm(): bool
    {
        $name = $this->sanitizeInput($this->postData["name"] ?? '');
        $email = $this->sanitizeInput($this->postData["email"] ?? '');
        $phone = $this->sanitizeInput($this->postData["phone"] ?? '');
        $service = $this->sanitizeInput($this->postData["subject"] ?? '');
        $message = $this->sanitizeInput($this->postData["message"] ?? '');
        $recaptchaResponse = $this->sanitizeInput($this->postData["g-recaptcha-response"] ?? '');

        $this->validateInputs($name, $email, $phone, $service, $message, $recaptchaResponse);

        if (!empty($this->errors)) {
            $this->errorMessage = "<div class='alert alert-danger'>" . implode('<br>', $this->errors) . "</div>";
            return false;
        }

        if ($this->queueEmail($name, $email, $phone, $service, $message)) {
            $this->postData = [];
            return true;
        }

        return false;
    }

    private function validateInputs(
        string $name,
        string $email,
        string $phone,
        string $service,
        string $message,
        string $recaptchaResponse
    ): void {
        $this->errors = [];

        if (empty($name)) {
            $this->errors[] = "Name is required.";
        }

        if (empty($email)) {
            $this->errors[] = "Email is required.";
        }

        if (empty($phone)) {
            $this->errors[] = "Phone number is required.";
        }

        if (empty($service)) {
            $this->errors[] = "Please select a service.";
        }

        if (empty($message)) {
            $this->errors[] = "Message is required.";
        }

        $recaptchaErrors = $this->validateRecaptcha($recaptchaResponse);
        $this->errors = array_merge($this->errors, $recaptchaErrors);
    }

    private function validateRecaptcha(string $recaptchaResponse): array
    {
        $errors = [];
        $secretKey = '6LdAQycrAAAAABql1O8Im94AF2vuJz29p5Gezrqh';

        if (empty($recaptchaResponse)) {
            $errors[] = "Please complete the reCAPTCHA verification.";
            return $errors;
        }

        $recaptchaURL = 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secretKey . '&response=' . $recaptchaResponse;
        $verifyResponse = file_get_contents($recaptchaURL);
        $responseData = json_decode($verifyResponse);

        if (!$responseData->success) {
            $errors[] = "reCAPTCHA verification failed. Please try again.";
        }

        return $errors;
    }

    private function queueEmail(
        string $name,
        string $email,
        string $phone,
        string $service,
        string $message
    ): bool {
        $toEmail = "fathii.alsadi@gmail.com";
        $emailSubject = "New contact form submission from $name";

        $htmlBody = "
            <h1>New message</h1>
            <p><strong>Name:</strong> $name</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Phone:</strong> $phone</p>
            <p><strong>Service:</strong> $service</p>
            <p><strong>Message:</strong> $message</p>
            <p><strong>Time:</strong> " . date('Y-m-d H:i:s') . "</p>
        ";

        $textBody = "Name: $name\nEmail: $email\nPhone: $phone\nService: $service\nMessage: $message";

        // Prepare data for queue
        $emailData = [
            'name' => $name,
            'email' => $email,
            'toEmail' => $toEmail,
            'subject' => $emailSubject,
            'body' => $htmlBody,
            'altBody' => $textBody,
            'metadata' => [
                'phone' => $phone,
                'service' => $service,
                'submitTime' => date('Y-m-d H:i:s')
            ]
        ];

        try {
            // Push to queue instead of sending immediately
            if ($this->queue->push($emailData)) {
                $this->successMessage = "<div class='alert alert-success'>Your message will be sent shortly!</div>";
                return true;
            } else {
                $this->errorMessage = "<div class='alert alert-danger'>There was a problem queuing your message. Please try again later.</div>";
                return false;
            }
        } catch (Exception $e) {
            error_log("Queue Error: " . $e->getMessage());
            $this->errorMessage = "<div class='alert alert-danger'>Message could not be queued. Please try again later.</div>";
            return false;
        }
    }

    private function sanitizeInput($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
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

        return $this->queueEmail($name, $email, $phone, $service, $message);
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