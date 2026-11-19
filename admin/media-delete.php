<?php
/**
 * Xlator Admin - Delete Media from Database
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';

require_admin_login();

header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$token = $_POST['csrf_token'] ?? '';
if (!verify_csrf_token($token)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Invalid security token']);
    exit;
}

$id = isset($_POST['id']) ? trim((string)$_POST['id']) : '';
if ($id === '' || !preg_match('/^[a-zA-Z0-9_-]+$/', $id)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid media ID']);
    exit;
}

// Check if any event is currently using this image
$image_path = 'image.php?id=' . $id;
$stmt = get_db()->prepare('SELECT COUNT(*) FROM events WHERE image = ? OR image = ?');
$stmt->execute([$image_path, '/' . $image_path]);
$count = (int)$stmt->fetchColumn();

if ($count > 0) {
    http_response_code(409);
    echo json_encode(['success' => false, 'error' => 'This image is currently used by an event and cannot be deleted. Change or remove it from the event first.']);
    exit;
}

$deleted = delete_media($id);
if ($deleted) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to delete media from database']);
}
exit;
