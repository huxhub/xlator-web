<?php
/**
 * Local Development Router for PHP Built-in Web Server
 * Mimics Hostinger's Apache .htaccess clean URL rewrites
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$filePath = __DIR__ . $uri;

// 1. Intercept /events.php/admin, /events/admin, etc. and redirect to /admin/
if (preg_match('#^/events(\.php)?/admin(/.*)?$#i', $uri, $m)) {
    $target = '/admin/' . (!empty($m[2]) ? ltrim($m[2], '/') : '');
    $qs = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
    header('Location: ' . $target . $qs, true, 302);
    exit;
}

// 2. Normalize /admin without trailing slash to /admin/
if ($uri === '/admin') {
    header('Location: /admin/');
    exit;
}

// 3. Direct rescue for accidental root-level admin actions (/event-edit, /event-delete, etc.)
if (preg_match('#^/(event-edit|event-delete|login|logout)(\.php)?$#', $uri, $m)) {
    $script = $m[1] . '.php';
    if (file_exists(__DIR__ . '/admin/' . $script)) {
        $qs = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
        header('Location: /admin/' . $script . $qs, true, 302);
        exit;
    }
}

// 4. Intercept static assets even if requested with a subpath prefix (e.g. /events.php/css/styles.css)
if (preg_match('#/(css|js|images|uploads)/(.+)$#i', $uri, $assetMatch)) {
    $assetPath = __DIR__ . '/' . $assetMatch[1] . '/' . $assetMatch[2];
    if (file_exists($assetPath) && !is_dir($assetPath)) {
        $ext = strtolower(pathinfo($assetPath, PATHINFO_EXTENSION));
        if ($ext === 'css') {
            header('Content-Type: text/css');
            readfile($assetPath);
            exit;
        }
        if ($ext === 'js') {
            header('Content-Type: application/javascript');
            readfile($assetPath);
            exit;
        }
        if ($ext === 'svg') {
            header('Content-Type: image/svg+xml');
            readfile($assetPath);
            exit;
        }
        if (in_array($ext, ['png', 'jpg', 'jpeg', 'webp', 'gif', 'ico'])) {
            $mimeMap = [
                'png'  => 'image/png',
                'jpg'  => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'webp' => 'image/webp',
                'gif'  => 'image/gif',
                'ico'  => 'image/x-icon'
            ];
            header('Content-Type: ' . ($mimeMap[$ext] ?? 'image/jpeg'));
            readfile($assetPath);
            exit;
        }
        return false;
    }
}

// 5. Static files directly at path
if ($uri !== '/' && file_exists($filePath) && !is_dir($filePath)) {
    return false;
}

// 6. Directory index handling (e.g. /admin/ -> admin/index.php)
if (is_dir($filePath) && file_exists(rtrim($filePath, '/') . '/index.php')) {
    require rtrim($filePath, '/') . '/index.php';
    exit;
}

// 7. Clean URLs for admin scripts: /admin/event-edit -> admin/event-edit.php
if (preg_match('#^/admin/([^/]+)/?$#', $uri, $matches)) {
    $adminScript = __DIR__ . '/admin/' . $matches[1] . '.php';
    if (file_exists($adminScript)) {
        require $adminScript;
        exit;
    }
}

// 8. Direct match for clean URL .php file (e.g. /program -> program.php)
if (file_exists($filePath . '.php')) {
    require $filePath . '.php';
    exit;
}

// 9. Dynamic event detail route: /events/<slug>
if (preg_match('#^/events/([^/]+)/?$#', $uri, $matches)) {
    $slug = $matches[1];
    // Check if a static php file exists in events/ first
    if (file_exists(__DIR__ . '/events/' . $slug . '.php')) {
        require __DIR__ . '/events/' . $slug . '.php';
        exit;
    }
    // Otherwise route dynamically to event-detail.php
    $_GET['slug'] = $slug;
    require __DIR__ . '/event-detail.php';
    exit;
}

// 10. Root URL
if ($uri === '/' || $uri === '' || $uri === '/index.php') {
    require __DIR__ . '/index.php';
    exit;
}

// 11. Default 404
http_response_code(404);
if (file_exists(__DIR__ . '/404.php')) {
    require __DIR__ . '/404.php';
} else {
    echo "404 Not Found";
}
exit;
