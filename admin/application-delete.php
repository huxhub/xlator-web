<?php
/** Delete one saved application after admin and CSRF checks. */
require_once __DIR__ . '/../includes/config.php';
require_admin_login();
require_once __DIR__ . '/../includes/applications-db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit;
}

$token = $_POST['csrf_token'] ?? '';
if (!is_string($token) || !verify_csrf_token($token)) {
    set_flash_message('error', 'Invalid security token. Please try again.');
} else {
    $id = is_string($_POST['id'] ?? null) ? trim($_POST['id']) : '';
    if ($id !== '' && delete_application($id)) {
        set_flash_message('success', 'Application deleted.');
    } else {
        set_flash_message('error', 'Application not found.');
    }
}

header('Location: /admin/applications.php', true, 303);
exit;
