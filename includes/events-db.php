<?php
require_once __DIR__ . '/database.php';

/**
 * @param array<string, mixed> $event
 * @return array<string, mixed>
 */
function normalize_event(array $event): array {
    $event['is_published'] = (bool)$event['is_published'];
    return $event;
}

/**
 * @param bool $include_unpublished
 * @return array<int, array<string, mixed>>
 */
function get_all_events(bool $include_unpublished = false): array {
    $sql = 'SELECT * FROM events' . ($include_unpublished ? '' : ' WHERE is_published = 1') . ' ORDER BY date ASC';
    return array_map('normalize_event', get_db()->query($sql)->fetchAll());
}

/**
 * @param string $id
 * @return array<string, mixed>|null
 */
function get_event_by_id(string $id): ?array {
    $stmt = get_db()->prepare('SELECT * FROM events WHERE id = ?');
    $stmt->execute([$id]);
    $event = $stmt->fetch();
    return $event ? normalize_event($event) : null;
}

/**
 * @param string $slug
 * @return array<string, mixed>|null
 */
function get_event_by_slug(string $slug): ?array {
    $stmt = get_db()->prepare('SELECT * FROM events WHERE slug = ?');
    $stmt->execute([$slug]);
    $event = $stmt->fetch();
    return $event ? normalize_event($event) : null;
}

/**
 * @param int|null $limit
 * @return array<int, array<string, mixed>>
 */
function get_upcoming_events(?int $limit = null): array {
    $events = array_values(array_filter(get_all_events(), function (array $event): bool {
        return $event['date'] >= date('Y-m-d');
    }));
    return $limit === null ? $events : array_slice($events, 0, $limit);
}

/**
 * @param int|null $limit
 * @return array<int, array<string, mixed>>
 */
function get_past_events(?int $limit = null): array {
    $events = array_values(array_filter(get_all_events(), function (array $event): bool {
        return $event['date'] < date('Y-m-d');
    }));
    usort($events, function (array $a, array $b): int { return strcmp($b['date'], $a['date']); });
    return $limit === null ? $events : array_slice($events, 0, $limit);
}

/**
 * @param array<string, mixed> $data
 * @return array<string, mixed>
 */
function save_event(array $data): array {
    $now = date('Y-m-d H:i:s');
    $id = !empty($data['id']) ? (string)$data['id'] : uniqid('evt_', true);
    $existing = get_event_by_id($id);
    $title = trim((string)($data['title'] ?? 'Untitled Event'));
    $slug = trim((string)($data['slug'] ?? ''));
    if ($slug === '') $slug = strtolower(trim((string)preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
    $date = trim((string)($data['date'] ?? date('Y-m-d')));
    $timestamp = strtotime($date);
    $event = [
        'id' => $id, 'slug' => $slug, 'title' => $title,
        'category' => trim((string)($data['category'] ?? 'Offline Session')),
        'summary' => trim((string)($data['summary'] ?? '')),
        'description' => trim((string)($data['description'] ?? '')),
        'date' => $date,
        'date_display' => $timestamp ? date('D, j M, Y', $timestamp) : $date,
        'day' => $timestamp ? date('d', $timestamp) : date('d'),
        'month' => $timestamp ? strtoupper(date('M', $timestamp)) : strtoupper(date('M')),
        'time' => trim((string)($data['time'] ?? '10:00 am – 5:00 pm')),
        'location' => trim((string)($data['location'] ?? 'Kochi, Kerala')),
        'format' => trim((string)($data['format'] ?? 'Offline')),
        'image' => !empty($data['image']) ? trim((string)$data['image']) : 'images/collaboration.jpg',
        'is_published' => (int)!empty($data['is_published']),
        'created_at' => $existing['created_at'] ?? $now, 'updated_at' => $now
    ];
    $columns = array_keys($event);
    $updates = implode(', ', array_map(function ($column) { return "$column = excluded.$column"; },
        array_filter($columns, function ($column) { return $column !== 'id' && $column !== 'created_at'; })));
    $stmt = get_db()->prepare('INSERT INTO events (' . implode(', ', $columns) . ') VALUES (' .
        implode(', ', array_fill(0, count($columns), '?')) . ") ON CONFLICT(id) DO UPDATE SET $updates");
    $stmt->execute(array_values($event));
    $event['is_published'] = (bool)$event['is_published'];
    return $event;
}

/**
 * @param string $id
 * @return bool
 */
function delete_event(string $id): bool {
    $stmt = get_db()->prepare('DELETE FROM events WHERE id = ?');
    return $stmt->execute([$id]);
}
