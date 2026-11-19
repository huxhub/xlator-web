<?php
/**
 * Xlator Admin - Delete Event Handler
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/events-db.php';

require_admin_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($token)) {
        set_flash_message('error', 'Invalid security token.');
    } else {
        $id = trim($_POST['id'] ?? '');
        if (!empty($id)) {
            delete_event($id);
            set_flash_message('success', 'Event successfully deleted.');
        }
    }
}

header('Location: /admin/index.php');
exit;
