<?php
/**
 * Xlator - Application Submission Handler (PHP)
 * Saves leads to SQLite and sends notification emails on Hostinger.
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/mailer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed.']);
    exit;
}

// Anti-spam honeypot
if (!empty($_POST['website_url'])) {
    // Silent success for bots
    echo json_encode(['success' => true, 'message' => 'Application received.']);
    exit;
}

// Sanitize inputs
$name          = trim(strip_tags($_POST['name'] ?? ''));
$email         = trim(strip_tags($_POST['email'] ?? ''));
$phone         = trim(strip_tags($_POST['phone'] ?? ''));
$business_name = trim(strip_tags($_POST['business_name'] ?? ''));
$path          = trim(strip_tags($_POST['path'] ?? 'Undecided'));
$message       = trim(strip_tags($_POST['message'] ?? ''));

// Validate required fields
if (empty($name)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Please provide your full name.']);
    exit;
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Please provide a valid email address.']);
    exit;
}

// Valid paths
$allowed_paths = ['Xlator', 'Xlator Elite', 'Undecided'];
if (!in_array($path, $allowed_paths, true)) {
    $path = 'Undecided';
}

$submission = [
    'id'            => uniqid('app_', true),
    'timestamp'     => date('Y-m-d H:i:s'),
    'name'          => $name,
    'email'         => $email,
    'phone'         => $phone,
    'business_name' => $business_name,
    'path'          => $path,
    'message'       => $message,
    'ip_address'    => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
    'user_agent'    => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
];

try {
    $stmt = get_db()->prepare('INSERT INTO applications
        (id, timestamp, name, email, phone, business_name, path, message, ip_address, user_agent)
        VALUES (:id, :timestamp, :name, :email, :phone, :business_name, :path, :message, :ip_address, :user_agent)');
    $stmt->execute($submission);
} catch (Throwable $e) {
    error_log('Application database error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Unable to save the application. Please try again.']);
    exit;
}

// The lead stays saved even if the notification service is unavailable.
$notification_sent = false;
try {
    send_application_notification($submission);
    $notification_sent = true;
} catch (Throwable $e) {
    error_log('Application notification failed for ' . $submission['id'] . ': ' . $e->getMessage());
}

// Return JSON response
echo json_encode([
    'success' => true,
    'notification_sent' => $notification_sent,
    'contact_email' => $notification_sent ? null : ADMIN_EMAIL,
    'message' => 'Thank you for applying to Brand Xlator! Your application has been received.'
]);
exit;
