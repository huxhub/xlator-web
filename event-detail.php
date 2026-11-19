<?php
/**
 * Xlator - Dynamic Event Detail Page
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/events-db.php';

$slug = trim($_GET['slug'] ?? '');
$id = trim($_GET['id'] ?? '');

$event = null;
if (!empty($slug)) {
    $event = get_event_by_slug($slug);
} elseif (!empty($id)) {
    $event = get_event_by_id($id);
}

if (!$event || (empty($event['is_published']) && !is_admin_logged_in())) {
    http_response_code(404);
    require_once __DIR__ . '/404.php';
    exit;
}

$page_title = htmlspecialchars($event['title'] . ' — Xlator Events', ENT_QUOTES, 'UTF-8');
$page_description = htmlspecialchars($event['summary'], ENT_QUOTES, 'UTF-8');
$current_page = 'events';

include __DIR__ . '/includes/header.php';
?>
  <main class="flex-1">
    <article>
      <section class="x-motif overflow-hidden">
        <div class="mx-auto max-w-7xl px-5 pt-10 md:px-10">
          <a class="eyebrow inline-flex items-center gap-2 text-muted-foreground hover:text-foreground" href="/events.php">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left size-4" aria-hidden="true"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg>
            All events
          </a>
        </div>

        <div class="mx-auto grid max-w-7xl items-start gap-12 px-5 py-10 md:grid-cols-[1.1fr_1fr] md:px-10 md:py-14">
          <div>
            <span class="eyebrow rounded-full bg-ink px-3 py-1.5 text-[0.65rem] text-ink-foreground">
              <?php echo htmlspecialchars($event['category'] ?? 'Session', ENT_QUOTES, 'UTF-8'); ?>
            </span>
            <h1 class="headline mt-6 text-4xl md:text-6xl">
              <?php echo htmlspecialchars($event['title'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
            </h1>
            <p class="mt-6 text-lg text-muted-foreground">
              <?php echo htmlspecialchars($event['summary'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
            </p>

            <dl class="mt-8 grid gap-4 sm:grid-cols-2">
              <div class="flex gap-3 rounded-xl border border-border bg-card p-4 [&_svg]:size-5 [&_svg]:text-brand">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar-days" aria-hidden="true"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect width="18" height="18" x="3" y="4" rx="2"></rect><path d="M3 10h18"></path><path d="M8 14h.01"></path><path d="M12 14h.01"></path><path d="M16 14h.01"></path><path d="M8 18h.01"></path><path d="M12 18h.01"></path><path d="M16 18h.01"></path></svg>
                <div>
                  <dt class="eyebrow text-[0.6rem] text-muted-foreground">Date</dt>
                  <dd class="mt-1 font-semibold"><?php echo htmlspecialchars($event['date_display'] ?? $event['date'] ?? '', ENT_QUOTES, 'UTF-8'); ?></dd>
                </div>
              </div>

              <div class="flex gap-3 rounded-xl border border-border bg-card p-4 [&_svg]:size-5 [&_svg]:text-brand">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="M12 6v6l4 2"></path></svg>
                <div>
                  <dt class="eyebrow text-[0.6rem] text-muted-foreground">Time (IST)</dt>
                  <dd class="mt-1 font-semibold"><?php echo htmlspecialchars($event['time'] ?? '', ENT_QUOTES, 'UTF-8'); ?></dd>
                </div>
              </div>

              <div class="flex gap-3 rounded-xl border border-border bg-card p-4 [&_svg]:size-5 [&_svg]:text-brand">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin" aria-hidden="true"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path><circle cx="12" cy="10" r="3"></circle></svg>
                <div>
                  <dt class="eyebrow text-[0.6rem] text-muted-foreground">Location</dt>
                  <dd class="mt-1 font-semibold"><?php echo htmlspecialchars($event['location'] ?? '', ENT_QUOTES, 'UTF-8'); ?></dd>
                </div>
              </div>

              <div class="flex gap-3 rounded-xl border border-border bg-card p-4 [&_svg]:size-5 [&_svg]:text-brand">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-monitor" aria-hidden="true"><rect width="20" height="14" x="2" y="3" rx="2"></rect><line x1="8" x2="16" y1="21" y2="21"></line><line x1="12" x2="12" y1="17" y2="21"></line></svg>
                <div>
                  <dt class="eyebrow text-[0.6rem] text-muted-foreground">Format</dt>
                  <dd class="mt-1 font-semibold"><?php echo htmlspecialchars($event['format'] ?? '', ENT_QUOTES, 'UTF-8'); ?></dd>
                </div>
              </div>
            </dl>

            <div class="mt-10 flex flex-wrap gap-3">
              <a class="inline-flex items-center justify-center gap-2 whitespace-nowrap cursor-pointer transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 disabled:cursor-not-allowed [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 rounded-full bg-brand text-brand-foreground uppercase tracking-wider font-semibold hover:bg-brand/90 shadow-[var(--shadow-lift)] h-12 px-7 text-sm" href="/join.php?event=<?php echo urlencode($event['title'] ?? ''); ?>">
                Reserve my spot 
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-right" aria-hidden="true"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
              </a>
              <?php if (is_admin_logged_in()): ?>
                <a class="inline-flex items-center justify-center gap-2 rounded-full border border-border px-5 py-2.5 text-xs font-semibold hover:bg-muted" href="/admin/event-edit.php?id=<?php echo urlencode($event['id'] ?? ''); ?>">
                  Edit in Admin
                </a>
              <?php endif; ?>
            </div>
          </div>

          <img src="<?php echo htmlspecialchars(get_asset_url($event['image'] ?? 'images/collaboration.jpg'), ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($event['title'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="w-full rounded-3xl object-cover shadow-[var(--shadow-lift)] photo-mono"/>
        </div>
      </section>

      <?php if (!empty($event['description'])): ?>
        <section class="mx-auto max-w-3xl px-5 pb-24 md:px-10">
          <h2 class="eyebrow text-brand">About this <?php echo htmlspecialchars(strtolower($event['category'] ?? 'session'), ENT_QUOTES, 'UTF-8'); ?></h2>
          <div class="mt-5 space-y-4 whitespace-pre-line text-lg leading-relaxed">
            <?php echo htmlspecialchars($event['description'], ENT_QUOTES, 'UTF-8'); ?>
          </div>
        </section>
      <?php endif; ?>
    </article>
  </main>

<?php include __DIR__ . '/includes/footer.php'; ?>
