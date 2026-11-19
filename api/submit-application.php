<?php
/**
 * Xlator - Application Submission Handler (PHP)
 * Saves leads to JSON & CSV and sends notification emails on Hostinger.
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../includes/config.php';

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

// Ensure data directory exists
$data_dir = __DIR__ . '/../data';
if (!is_dir($data_dir)) {
    mkdir($data_dir, 0755, true);
}

// 1. Save to JSON store
$json_file = $data_dir . '/applications.json';
$existing = [];
if (file_exists($json_file)) {
    $content = file_get_contents($json_file);
    $existing = json_decode($content, true) ?: [];
}
$existing[] = $submission;
file_put_contents($json_file, json_encode($existing, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), LOCK_EX);

// 2. Save to CSV for easy Excel export
$csv_file = $data_dir . '/leads.csv';
$csv_is_new = !file_exists($csv_file);
$fp = fopen($csv_file, 'a');
if ($fp) {
    if ($csv_is_new) {
        fputcsv($fp, ['ID', 'Date', 'Full Name', 'Email', 'Phone', 'Business Name', 'Path', 'Message', 'IP']);
    }
    fputcsv($fp, [
        $submission['id'],
        $submission['timestamp'],
        $name,
        $email,
        $phone,
        $business_name,
        $path,
        $message,
        $submission['ip_address']
    ]);
    fclose($fp);
}

// 3. Send email notification via Hostinger PHP mail()
$to = ADMIN_EMAIL;
$subject = "New Brand Xlator Application: $name ($path)";

$email_body = "New Brand Xlator Mentorship Application\n";
$email_body .= "=======================================\n\n";
$email_body .= "Path:          $path\n";
$email_body .= "Full Name:     $name\n";
$email_body .= "Email:         $email\n";
$email_body .= "Phone:         $phone\n";
$email_body .= "Business:      $business_name\n";
$email_body .= "Submitted At:  " . $submission['timestamp'] . "\n\n";
$email_body .= "Brand Details / Message:\n";
$email_body .= "$message\n\n";
$email_body .= "=======================================\n";

$headers = "From: no-reply@" . ($_SERVER['SERVER_NAME'] ?? 'xlator.com') . "\r\n";
$headers .= "Reply-To: " . $email . "\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

// Send email (suppress error if mail server isn't configured in dev environment)
@mail($to, $subject, $email_body, $headers);

// Return JSON response
echo json_encode([
    'success' => true,
    'message' => 'Thank you for applying to Brand Xlator! Your application has been received.'
]);
exit;
