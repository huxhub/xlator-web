<?php
require_once __DIR__ . '/config.php';

/**
 * Get or initialize SQLite PDO database connection.
 *
 * @return PDO
 */
function get_db(): PDO {
    static $db = null;
    if ($db !== null) return $db;
    if (!extension_loaded('pdo_sqlite')) throw new RuntimeException('PDO SQLite extension is required.');
    if (!is_dir(DATA_DIR) && !mkdir(DATA_DIR, 0755, true) && !is_dir(DATA_DIR)) throw new RuntimeException('Cannot create data directory.');
    $db = new PDO('sqlite:' . DATA_DIR . '/xlator.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $db->exec('PRAGMA busy_timeout = 5000');
    $db->exec('CREATE TABLE IF NOT EXISTS events (id TEXT PRIMARY KEY, slug TEXT NOT NULL UNIQUE, title TEXT NOT NULL, category TEXT NOT NULL, summary TEXT NOT NULL, description TEXT NOT NULL, date TEXT NOT NULL, date_display TEXT NOT NULL, day TEXT NOT NULL, month TEXT NOT NULL, time TEXT NOT NULL, location TEXT NOT NULL, format TEXT NOT NULL, image TEXT NOT NULL, is_published INTEGER NOT NULL, created_at TEXT NOT NULL, updated_at TEXT NOT NULL)');
    $db->exec('CREATE TABLE IF NOT EXISTS applications (id TEXT PRIMARY KEY, timestamp TEXT NOT NULL, name TEXT NOT NULL, email TEXT NOT NULL, phone TEXT NOT NULL, business_name TEXT NOT NULL, path TEXT NOT NULL, message TEXT NOT NULL, ip_address TEXT NOT NULL, user_agent TEXT NOT NULL)');
    $db->exec('CREATE TABLE IF NOT EXISTS media (id TEXT PRIMARY KEY, filename TEXT NOT NULL, mime_type TEXT NOT NULL, data BLOB NOT NULL, size INTEGER NOT NULL, created_at TEXT NOT NULL)');
    $db->exec('CREATE TABLE IF NOT EXISTS migrations (name TEXT PRIMARY KEY)');
    $db->beginTransaction();
    try {
        $done = $db->query('SELECT name FROM migrations')->fetchAll(PDO::FETCH_COLUMN);
        if (!in_array('events_json', $done, true)) {
            import_legacy_json($db, DATA_DIR . '/events.json', 'events', ['id','slug','title','category','summary','description','date','date_display','day','month','time','location','format','image','is_published','created_at','updated_at']);
            $db->exec("INSERT INTO migrations VALUES ('events_json')");
        }
        if (!in_array('applications_json', $done, true)) {
            import_legacy_json($db, DATA_DIR . '/applications.json', 'applications', ['id','timestamp','name','email','phone','business_name','path','message','ip_address','user_agent']);
            $db->exec("INSERT INTO migrations VALUES ('applications_json')");
        }
        if (!in_array('leads_csv', $done, true)) {
            import_legacy_leads_csv($db, DATA_DIR . '/leads.csv');
            $db->exec("INSERT INTO migrations VALUES ('leads_csv')");
        }
        if (!in_array('media_table', $done, true)) {
            $uploads_dir = dirname(__DIR__) . '/uploads/events';
            if (is_dir($uploads_dir)) {
                $files = scandir($uploads_dir);
                if ($files !== false) {
                    foreach ($files as $f) {
                        if ($f === '.' || $f === '..' || str_starts_with($f, '.')) continue;
                        $filePath = $uploads_dir . '/' . $f;
                        if (!is_file($filePath)) continue;
                        $content = file_get_contents($filePath);
                        if ($content === false) continue;
                        $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
                        $mime = match($ext) {
                            'jpg', 'jpeg' => 'image/jpeg',
                            'png' => 'image/png',
                            'webp' => 'image/webp',
                            'gif' => 'image/gif',
                            default => 'application/octet-stream'
                        };
                        $id = 'img_' . bin2hex(random_bytes(10));
                        $stmt = $db->prepare('INSERT OR IGNORE INTO media (id, filename, mime_type, data, size, created_at) VALUES (?, ?, ?, ?, ?, ?)');
                        $stmt->bindValue(1, $id, PDO::PARAM_STR);
                        $stmt->bindValue(2, $f, PDO::PARAM_STR);
                        $stmt->bindValue(3, $mime, PDO::PARAM_STR);
                        $stmt->bindValue(4, $content, PDO::PARAM_LOB);
                        $stmt->bindValue(5, strlen($content), PDO::PARAM_INT);
                        $stmt->bindValue(6, date('Y-m-d H:i:s'), PDO::PARAM_STR);
                        $stmt->execute();

                        $oldRelPath = 'uploads/events/' . $f;
                        $newPath = 'image.php?id=' . $id;
                        $upStmt = $db->prepare('UPDATE events SET image = ? WHERE image = ?');
                        $upStmt->execute([$newPath, $oldRelPath]);

                        @unlink($filePath);
                    }
                }
            }
            $db->exec("INSERT INTO migrations VALUES ('media_table')");
        }
        $db->commit();
    } catch (Throwable $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        $db = null;
        throw $e;
    }
    return $db;
}

/**
 * Import rows from legacy JSON file into SQLite table.
 *
 * @param PDO $db
 * @param string $file
 * @param string $table
 * @param array<int, string> $columns
 * @return void
 */
function import_legacy_json(PDO $db, string $file, string $table, array $columns): void {
    if (!file_exists($file)) return;
    $rows = json_decode((string)file_get_contents($file), true);
    if (!is_array($rows)) throw new RuntimeException('Invalid legacy data in ' . basename($file));
    $fields = implode(', ', $columns);
    $params = implode(', ', array_fill(0, count($columns), '?'));
    $stmt = $db->prepare("INSERT OR IGNORE INTO $table ($fields) VALUES ($params)");
    foreach ($rows as $row) {
        $values = [];
        foreach ($columns as $column) $values[] = $column === 'is_published' ? (int)!empty($row[$column]) : ($row[$column] ?? '');
        $stmt->execute($values);
    }
}

/**
 * Import rows from legacy leads CSV file into SQLite applications table.
 *
 * @param PDO $db
 * @param string $file
 * @return void
 */
function import_legacy_leads_csv(PDO $db, string $file): void {
    if (!file_exists($file)) return;
    $handle = fopen($file, 'r');
    if ($handle === false) throw new RuntimeException('Cannot read legacy leads CSV.');
    try {
        $stmt = $db->prepare('INSERT OR IGNORE INTO applications (id,timestamp,name,email,phone,business_name,path,message,ip_address,user_agent) VALUES (?,?,?,?,?,?,?,?,?,?)');
        fgetcsv($handle);
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) >= 9 && $row[0] !== '') $stmt->execute(array_merge(array_slice($row, 0, 9), ['']));
        }
    } finally {
        fclose($handle);
    }
}

/**
 * Save binary image data into the media database table.
 *
 * @param string $filename
 * @param string $mime_type
 * @param string $binary_data
 * @return string Media record ID
 */
function save_media_blob(string $filename, string $mime_type, string $binary_data): string {
    $db = get_db();
    $id = 'img_' . bin2hex(random_bytes(10));
    $now = date('Y-m-d H:i:s');
    $stmt = $db->prepare('INSERT INTO media (id, filename, mime_type, data, size, created_at) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->bindValue(1, $id, PDO::PARAM_STR);
    $stmt->bindValue(2, $filename, PDO::PARAM_STR);
    $stmt->bindValue(3, $mime_type, PDO::PARAM_STR);
    $stmt->bindValue(4, $binary_data, PDO::PARAM_LOB);
    $stmt->bindValue(5, strlen($binary_data), PDO::PARAM_INT);
    $stmt->bindValue(6, $now, PDO::PARAM_STR);
    $stmt->execute();
    return $id;
}

/**
 * Get media item by ID.
 *
 * @param string $id
 * @return array<string, mixed>|null
 */
function get_media_by_id(string $id): ?array {
    $stmt = get_db()->prepare('SELECT id, filename, mime_type, data, size, created_at FROM media WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

/**
 * Get all media items metadata (without loading full BLOBs) for gallery list.
 *
 * @return array<int, array<string, mixed>>
 */
function get_all_media_meta(): array {
    return get_db()->query('SELECT id, filename, mime_type, size, created_at FROM media ORDER BY created_at DESC')->fetchAll();
}

/**
 * Delete a media item by ID.
 *
 * @param string $id
 * @return bool
 */
function delete_media(string $id): bool {
    $stmt = get_db()->prepare('DELETE FROM media WHERE id = ?');
    return $stmt->execute([$id]);
}
