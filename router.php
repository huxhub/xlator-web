<?php
/**
 * Local Development Router for PHP Built-in Web Server
 * Mimics Hostinger's Apache .htaccess clean URL rewrites and route handling
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$filePath = __DIR__ . $uri;

// 1. Universal Admin Interceptor:
// If /admin is accessed after any page/path (e.g. /program.php/admin, /events.php/admin, /anything/admin, /program/admin/event-edit.php)
if (preg_match('#^/(?!admin(?:/|$)).*?/admin(?:/(.*))?$#i', $uri, $m)) {
    $subpath = !empty($m[1]) ? ltrim($m[1], '/') : '';
    $qs = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
    header('Location: /admin/' . $subpath . $qs, true, 302);
    exit;
}

// 2. Extensions / Aliases for Admin
// e.g., /admin.php, /admin.html, /administrator, /backend, /cp
if (preg_match('#^/admin\.(php|html?)$#i', $uri) || preg_match('#^/(administrator|backend|cp)(?:/(.*))?$#i', $uri, $m)) {
    $subpath = !empty($m[2]) ? ltrim($m[2], '/') : '';
    $qs = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
    header('Location: /admin/' . $subpath . $qs, true, 301);
    exit;
}

// 3. Normalize /admin without trailing slash to /admin/
if ($uri === '/admin') {
    $qs = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
    header('Location: /admin/' . $qs, true, 301);
    exit;
}

// 4. Direct rescue for accidental root-level admin actions (/event-edit, /event-delete, /login, /logout)
if (preg_match('#^/(event-edit|event-delete|login|logout)(\.php)?$#i', $uri, $m)) {
    $script = strtolower($m[1]) . '.php';
    if (file_exists(__DIR__ . '/admin/' . $script)) {
        $qs = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
        header('Location: /admin/' . $script . $qs, true, 302);
        exit;
    }
}

// 5. Intercept static assets even if requested with any subpath prefix (e.g. /program.php/css/styles.css)
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

// 6. Static files directly at path
if ($uri !== '/' && file_exists($filePath) && !is_dir($filePath)) {
    return false;
}

// 7. Directory index handling (e.g. /admin/ -> admin/index.php)
if (is_dir($filePath) && file_exists(rtrim($filePath, '/') . '/index.php')) {
    require rtrim($filePath, '/') . '/index.php';
    exit;
}

// 8. Admin Route Aliases and Clean URLs
if (preg_match('#^/admin/(events|dashboard|home)/?$#i', $uri)) {
    require __DIR__ . '/admin/index.php';
    exit;
}
if (preg_match('#^/admin/(new|create|add)/?$#i', $uri)) {
    require __DIR__ . '/admin/event-edit.php';
    exit;
}
if (preg_match('#^/admin/delete/?$#i', $uri)) {
    require __DIR__ . '/admin/event-delete.php';
    exit;
}
if (preg_match('#^/admin/signin/?$#i', $uri)) {
    require __DIR__ . '/admin/login.php';
    exit;
}
if (preg_match('#^/admin/signout/?$#i', $uri)) {
    require __DIR__ . '/admin/logout.php';
    exit;
}

// 9. Clean URLs for admin scripts: /admin/<script> -> admin/<script>.php
if (preg_match('#^/admin/([^/]+)/?$#i', $uri, $matches)) {
    $adminScript = __DIR__ . '/admin/' . $matches[1] . '.php';
    if (file_exists($adminScript)) {
        require $adminScript;
        exit;
    }
    // Unknown subpath under /admin/ -> safely redirect to /admin/
    header('Location: /admin/', true, 302);
    exit;
}

// 10. Direct match for clean URL .php file (e.g. /program -> program.php)
if (file_exists($filePath . '.php')) {
    require $filePath . '.php';
    exit;
}

// 11. Dynamic event detail route: /events/<slug>
if (preg_match('#^/events/([^/]+)/?$#i', $uri, $matches)) {
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

// 12. Root URL
if ($uri === '/' || $uri === '' || $uri === '/index.php') {
    require __DIR__ . '/index.php';
    exit;
}

// 13. Default 404
http_response_code(404);
if (file_exists(__DIR__ . '/404.php')) {
    require __DIR__ . '/404.php';
} else {
    echo "404 Not Found";
}
exit;
