<?php
// src/App/Queue/EmailJob.php
namespace App\Queue;


use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class EmailJob
{
    public function process(array $data): bool
    {
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


            $mail->setFrom($data['email'], $data['name']);
            $mail->addAddress($data['toEmail']);
            $mail->addReplyTo($data['email'], $data['name']);


            $mail->isHTML(true);
            $mail->Subject = $data['subject'];
            $mail->Body = $data['body'];
            $mail->AltBody = $data['altBody'];

            return $mail->send();
        } catch (Exception $e) {
            error_log("Queue EmailJob Error: " . $e->getMessage());
            return false;
        }
    }
}
