<?php
/**
 * Xlator - Events Data Access Layer
 * Zero-setup JSON database for managing events on Hostinger.
 */

if (!defined('DATA_DIR')) {
    require_once __DIR__ . '/config.php';
}

function get_events_file() {
    $dir = DATA_DIR;
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    return $dir . '/events.json';
}

/**
 * Seed initial events if database does not exist
 */
function init_events_db() {
    $file = get_events_file();
    if (!file_exists($file)) {
        $initial_events = [
            [
                'id'           => 'evt_brand_identity_2026',
                'slug'         => 'brand-identity-masterclass-dec-2026',
                'title'        => 'Brand Identity Masterclass',
                'category'     => 'Offline Session',
                'summary'      => "A hands-on day on finding your brand's purpose, positioning and voice with Brand Swamy & Faisal Cheeran.",
                'description'  => "Step one of the Xlator framework: Build Identity. In this offline session, business owners work through purpose, positioning, audience and brand voice — leaving with a clear identity brief they can act on the very next day.\n\nWho should attend: founders, D2C & retail owners, first-generation entrepreneurs and family business successors.",
                'date'         => '2026-12-12',
                'date_display' => 'Sat, 12 Dec, 2026',
                'day'          => '12',
                'month'        => 'DEC',
                'time'         => '10:00 am – 5:00 pm',
                'location'     => 'Kochi, Kerala',
                'format'       => 'Offline',
                'image'        => 'images/collaboration.jpg',
                'is_published' => true,
                'created_at'   => '2026-11-12 08:00:00',
                'updated_at'   => '2026-11-12 08:00:00'
            ],
            [
                'id'           => 'evt_open_house_2027',
                'slug'         => 'xlator-open-house-jan-2027',
                'title'        => 'Xlator Open House',
                'category'     => 'Program Info Session',
                'summary'      => 'Meet the mentors, understand the Xlator and Xlator Elite paths, and ask anything before you join.',
                'description'  => "An open evening for business owners considering the Brand Xlator mentorship programme. We walk through the five-stage framework, the 100+ hours of learning, offline sessions and the Elite benefits — followed by an open Q&A with the mentors.",
                'date'         => '2027-01-09',
                'date_display' => 'Sat, 9 Jan, 2027',
                'day'          => '09',
                'month'        => 'JAN',
                'time'         => '6:00 pm – 8:00 pm',
                'location'     => 'Online',
                'format'       => 'Online',
                'image'        => 'images/collaboration.jpg',
                'is_published' => true,
                'created_at'   => '2026-11-12 08:00:00',
                'updated_at'   => '2026-11-12 08:00:00'
            ],
            [
                'id'           => 'evt_quarterly_review_2027',
                'slug'         => 'quarterly-marketing-review-feb-2027',
                'title'        => 'Quarterly Marketing Review Clinic',
                'category'     => 'Elite Session',
                'summary'      => 'Bring your marketing plan. Leave with a sharper one. A review & performance analysis session for Elite members.',
                'description'  => "Xlator Elite members review the last quarter against their marketing impact dashboard, identify what moved the needle and set the plan for the next quarter with mentor feedback.",
                'date'         => '2027-02-06',
                'date_display' => 'Sat, 6 Feb, 2027',
                'day'          => '06',
                'month'        => 'FEB',
                'time'         => '10:00 am – 2:00 pm',
                'location'     => 'Kochi, Kerala',
                'format'       => 'Offline',
                'image'        => 'images/collaboration.jpg',
                'is_published' => true,
                'created_at'   => '2026-11-12 08:00:00',
                'updated_at'   => '2026-11-12 08:00:00'
            ]
        ];
        file_put_contents($file, json_encode($initial_events, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), LOCK_EX);
    }
}

/**
 * Retrieve all events
 */
function get_all_events($include_unpublished = false) {
    init_events_db();
    $file = get_events_file();
    if (!file_exists($file)) {
        return [];
    }
    $events = json_decode(file_get_contents($file), true) ?: [];
    
    if (!$include_unpublished) {
        $events = array_filter($events, function($e) {
            return !empty($e['is_published']);
        });
    }
    
    // Sort by date ascending
    usort($events, function($a, $b) {
        return strcmp($a['date'] ?? '', $b['date'] ?? '');
    });
    
    return array_values($events);
}

/**
 * Retrieve a single event by ID
 */
function get_event_by_id($id) {
    $events = get_all_events(true);
    foreach ($events as $event) {
        if (($event['id'] ?? '') === $id) {
            return $event;
        }
    }
    return null;
}

/**
 * Retrieve a single event by slug
 */
function get_event_by_slug($slug) {
    $events = get_all_events(true);
    foreach ($events as $event) {
        if (($event['slug'] ?? '') === $slug) {
            return $event;
        }
    }
    return null;
}

/**
 * Filter upcoming events (date >= today)
 */
function get_upcoming_events($limit = null) {
    $all = get_all_events(false);
    $today = date('Y-m-d');
    $upcoming = array_filter($all, function($e) use ($today) {
        return ($e['date'] ?? '') >= $today;
    });
    
    usort($upcoming, function($a, $b) {
        return strcmp($a['date'] ?? '', $b['date'] ?? '');
    });
    
    if ($limit !== null) {
        return array_slice(array_values($upcoming), 0, $limit);
    }
    return array_values($upcoming);
}

/**
 * Filter past events (date < today)
 */
function get_past_events($limit = null) {
    $all = get_all_events(false);
    $today = date('Y-m-d');
    $past = array_filter($all, function($e) use ($today) {
        return ($e['date'] ?? '') < $today;
    });
    
    usort($past, function($a, $b) {
        return strcmp($b['date'] ?? '', $a['date'] ?? '');
    });
    
    if ($limit !== null) {
        return array_slice(array_values($past), 0, $limit);
    }
    return array_values($past);
}

/**
 * Save (create or update) an event
 */
function save_event($data) {
    init_events_db();
    $events = get_all_events(true);
    $file = get_events_file();
    
    $is_edit = !empty($data['id']);
    $id = $is_edit ? $data['id'] : uniqid('evt_', true);
    
    // Generate clean slug
    $title = trim($data['title'] ?? 'Untitled Event');
    $slug = trim($data['slug'] ?? '');
    if (empty($slug)) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
    }
    
    // Format date components
    $date = trim($data['date'] ?? date('Y-m-d'));
    $timestamp = strtotime($date);
    $day = $timestamp ? date('d', $timestamp) : date('d');
    $month = $timestamp ? strtoupper(date('M', $timestamp)) : strtoupper(date('M'));
    $date_display = $timestamp ? date('D, j M, Y', $timestamp) : $date;
    
    $event_record = [
        'id'           => $id,
        'slug'         => $slug,
        'title'        => $title,
        'category'     => trim($data['category'] ?? 'Offline Session'),
        'summary'      => trim($data['summary'] ?? ''),
        'description'  => trim($data['description'] ?? ''),
        'date'         => $date,
        'date_display' => $date_display,
        'day'          => $day,
        'month'        => $month,
        'time'         => trim($data['time'] ?? '10:00 am – 5:00 pm'),
        'location'     => trim($data['location'] ?? 'Kochi, Kerala'),
        'format'       => trim($data['format'] ?? 'Offline'),
        'image'        => !empty($data['image']) ? trim($data['image']) : 'images/collaboration.jpg',
        'is_published' => !empty($data['is_published']),
        'updated_at'   => date('Y-m-d H:i:s')
    ];
    
    $found = false;
    foreach ($events as $index => $item) {
        if ($item['id'] === $id) {
            $event_record['created_at'] = $item['created_at'] ?? date('Y-m-d H:i:s');
            $events[$index] = $event_record;
            $found = true;
            break;
        }
    }
    
    if (!$found) {
        $event_record['created_at'] = date('Y-m-d H:i:s');
        $events[] = $event_record;
    }
    
    file_put_contents($file, json_encode($events, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), LOCK_EX);
    return $event_record;
}

/**
 * Delete an event
 */
function delete_event($id) {
    init_events_db();
    $events = get_all_events(true);
    $file = get_events_file();
    
    $filtered = array_filter($events, function($e) use ($id) {
        return $e['id'] !== $id;
    });
    
    file_put_contents($file, json_encode(array_values($filtered), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), LOCK_EX);
    return true;
}
