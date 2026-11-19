<?php
/** Read saved join-form applications for the admin area. */
require_once __DIR__ . '/database.php';

/**
 * @return int
 */
function get_application_count(): int {
    return (int)get_db()->query('SELECT COUNT(*) FROM applications')->fetchColumn();
}

/**
 * @return array<int, array<string, mixed>>
 */
function get_all_applications(): array {
    return get_db()->query('SELECT id, timestamp, name, email, phone, business_name, path, message
        FROM applications ORDER BY timestamp DESC, id DESC')->fetchAll();
}

/**
 * @param string $id
 * @return bool
 */
function delete_application(string $id): bool {
    $statement = get_db()->prepare('DELETE FROM applications WHERE id = ?');
    $statement->execute([$id]);
    return $statement->rowCount() === 1;
}
