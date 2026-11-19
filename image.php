<?php
/**
 * Dynamic Media Delivery Endpoint
 * Streams image BLOBs directly from SQLite database with HTTP caching.
 */
require_once __DIR__ . '/includes/database.php';

$id = isset($_GET['id']) ? trim((string)$_GET['id']) : '';
if ($id === '' || !preg_match('/^[a-zA-Z0-9_-]+$/', $id)) {
    http_response_code(400);
    header('Content-Type: text/plain; charset=UTF-8');
    exit('Invalid image ID');
}

$media = get_media_by_id($id);
if (!$media) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=UTF-8');
    exit('Image not found');
}

$etag = '"' . md5($id . $media['created_at']) . '"';
header('ETag: ' . $etag);
header('Cache-Control: public, max-age=31536000, immutable');
header('Content-Type: ' . $media['mime_type']);
header('Content-Length: ' . $media['size']);
header('X-Content-Type-Options: nosniff');

if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) === $etag) {
    http_response_code(304);
    exit;
}

echo $media['data'];
exit;
