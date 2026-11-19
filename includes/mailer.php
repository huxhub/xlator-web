<?php
/** Send application notifications through authenticated SMTP. */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../vendor/phpmailer/src/Exception.php';
require_once __DIR__ . '/../vendor/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../vendor/phpmailer/src/SMTP.php';

/**
 * @param array<string, mixed> $submission
 * @return void
 */
function send_application_notification(array $submission): void {
    $username = trim(env('SMTP_USERNAME', ''));
    $password = env('SMTP_PASSWORD', '');
    if ($username === '') {
        throw new RuntimeException('SMTP_USERNAME must be configured.');
    }
    if ($password === '') {
        throw new RuntimeException('SMTP_PASSWORD must be configured.');
    }
    if (!filter_var($username, FILTER_VALIDATE_EMAIL) || !filter_var(ADMIN_EMAIL, FILTER_VALIDATE_EMAIL)) {
        throw new RuntimeException('SMTP_USERNAME and ADMIN_EMAIL must be valid email addresses.');
    }

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = trim(env('SMTP_HOST', 'smtp.gmail.com'));
    $mail->Port = (int)env('SMTP_PORT', 587);
    $mail->SMTPAuth = true;
    $mail->Username = $username;
    $mail->Password = $password;
    $mail->Timeout = 15;
    $security = strtolower(trim(env('SMTP_ENCRYPTION', 'tls')));
    if ($security === 'tls') {
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    } elseif ($security === 'ssl') {
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
    } else {
        throw new RuntimeException('SMTP_ENCRYPTION must be tls or ssl.');
    }

    // Use the authenticated mailbox as the From address.
    $mail->setFrom($username, 'Xlator Applications');
    $mail->addAddress(ADMIN_EMAIL);
    $mail->addReplyTo($submission['email'], $submission['name']);
    $mail->CharSet = 'UTF-8';
    $mail->Subject = 'New Brand Xlator Application: ' . $submission['name'] . ' (' . $submission['path'] . ')';
    $mail->Body = "New Brand Xlator Mentorship Application\n"
        . "=======================================\n\n"
        . 'Path: ' . $submission['path'] . "\n"
        . 'Full Name: ' . $submission['name'] . "\n"
        . 'Email: ' . $submission['email'] . "\n"
        . 'Phone: ' . $submission['phone'] . "\n"
        . 'Business: ' . $submission['business_name'] . "\n"
        . 'Submitted At: ' . $submission['timestamp'] . "\n\n"
        . "Brand Details / Message:\n" . $submission['message'] . "\n";
    if (!$mail->send()) {
        throw new RuntimeException($mail->ErrorInfo ?: 'SMTP delivery failed.');
    }
}
