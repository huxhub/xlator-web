<?php
/**
 * Xlator Admin - Events Management Dashboard
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/events-db.php';

require_admin_login();

$events = get_all_events(true);
$flash = get_flash_message();

// Calculate stats
$total_count = count($events);
$today = date('Y-m-d');
$upcoming_count = 0;
$past_count = 0;
$draft_count = 0;

foreach ($events as $e) {
    if (empty($e['is_published'])) {
        $draft_count++;
    } elseif (($e['date'] ?? '') >= $today) {
        $upcoming_count++;
    } else {
        $past_count++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Events Manager — Xlator Admin</title>
  <link rel="icon" type="image/png" href="../images/favicon.png"/>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="anonymous"/>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600;700;800;900&family=Barlow+Condensed:wght@400;500;600&display=swap"/>
  <link rel="stylesheet" href="../css/styles.css"/>
  <link rel="stylesheet" href="../css/admin.css"/>
</head>
<body class="bg-background text-foreground min-h-screen flex flex-col selection:bg-brand selection:text-brand-foreground">

  <!-- Admin Navigation Header -->
  <header class="sticky top-0 z-40 border-b border-border/70 bg-card/90 backdrop-blur">
    <div class="mx-auto flex h-20 max-w-7xl items-center justify-between px-5 md:px-10">
      <div class="flex items-center gap-6">
        <a href="/admin/index.php" class="h-10 inline-block">
          <img src="../images/logo-black.png" alt="Xlator" class="h-full w-auto" />
        </a>
        <span class="hidden sm:inline-block text-xs font-bold uppercase tracking-widest text-brand bg-brand/10 px-2.5 py-1 rounded">
          Admin Console
        </span>
      </div>

      <div class="flex items-center gap-4">
        <a href="/events.php" target="_blank" class="hidden sm:inline-flex items-center gap-1.5 text-xs eyebrow hover:text-brand transition-colors">
          View Live Site &nearr;
        </a>
        <div class="h-4 w-[1px] bg-border hidden sm:block"></div>
        <div class="text-xs text-muted-foreground hidden md:block">
          Logged in as <strong class="text-foreground"><?php echo htmlspecialchars($_SESSION['admin_user'] ?? 'admin', ENT_QUOTES, 'UTF-8'); ?></strong>
        </div>
        <a href="/admin/logout.php" class="inline-flex items-center justify-center rounded-full border border-border px-4 py-1.5 text-xs font-semibold hover:bg-muted transition-colors">
          Log Out
        </a>
      </div>
    </div>
  </header>

  <!-- Main Dashboard Content -->
  <main class="flex-1 py-10 px-5 md:px-10">
    <div class="mx-auto max-w-7xl">

      <!-- Flash Notification -->
      <?php if ($flash): ?>
        <div class="mb-6 rounded-2xl border p-4 text-sm font-medium animate-rise <?php echo $flash['type'] === 'success' ? 'border-emerald-500/40 bg-emerald-500/10 text-emerald-700' : 'border-brand/40 bg-brand/10 text-brand'; ?>">
          <?php echo htmlspecialchars($flash['text'], ENT_QUOTES, 'UTF-8'); ?>
        </div>
      <?php endif; ?>

      <!-- Header & Action Row -->
      <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-8 border-b border-border">
        <div>
          <p class="eyebrow text-brand">Content Management</p>
          <h1 class="headline mt-2 text-4xl md:text-5xl">Events &amp; Programs</h1>
          <p class="mt-2 text-muted-foreground">Manage offline sessions, workshops, open houses and clinics.</p>
        </div>

        <div class="flex items-center gap-3">
          <a href="/admin/event-edit.php" class="inline-flex items-center justify-center gap-2 whitespace-nowrap cursor-pointer transition-colors rounded-full bg-brand text-brand-foreground uppercase tracking-wider font-semibold hover:bg-brand/90 shadow-[var(--shadow-lift)] h-12 px-6 text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <line x1="12" y1="5" x2="12" y2="19"></line>
              <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Add New Event
          </a>
        </div>
      </div>

      <!-- Stats Bar -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 my-8">
        <div class="rounded-2xl border border-border bg-card p-5">
          <p class="eyebrow text-[0.65rem] text-muted-foreground">Total Events</p>
          <p class="numeral text-4xl mt-2"><?php echo str_pad($total_count, 2, '0', STR_PAD_LEFT); ?></p>
        </div>
        <div class="rounded-2xl border border-border bg-card p-5">
          <p class="eyebrow text-[0.65rem] text-muted-foreground">Upcoming</p>
          <p class="numeral text-4xl mt-2 text-emerald-600"><?php echo str_pad($upcoming_count, 2, '0', STR_PAD_LEFT); ?></p>
        </div>
        <div class="rounded-2xl border border-border bg-card p-5">
          <p class="eyebrow text-[0.65rem] text-muted-foreground">Past Sessions</p>
          <p class="numeral text-4xl mt-2 text-muted-foreground"><?php echo str_pad($past_count, 2, '0', STR_PAD_LEFT); ?></p>
        </div>
        <div class="rounded-2xl border border-border bg-card p-5">
          <p class="eyebrow text-[0.65rem] text-muted-foreground">Drafts</p>
          <p class="numeral text-4xl mt-2 text-amber-600"><?php echo str_pad($draft_count, 2, '0', STR_PAD_LEFT); ?></p>
        </div>
      </div>

      <!-- Events List / Table -->
      <div class="overflow-hidden rounded-3xl border border-border bg-card shadow-[var(--shadow-lift)]">
        <?php if (empty($events)): ?>
          <div class="p-12 text-center">
            <p class="headline text-2xl text-muted-foreground">No events found</p>
            <p class="mt-2 text-sm text-muted-foreground">Click "Add New Event" to create your first event.</p>
          </div>
        <?php else: ?>
          <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
              <thead>
                <tr class="bg-muted/60 border-b border-border">
                  <th class="py-4 px-6 eyebrow text-[0.65rem]">Date</th>
                  <th class="py-4 px-6 eyebrow text-[0.65rem]">Event Title &amp; Details</th>
                  <th class="py-4 px-6 eyebrow text-[0.65rem]">Category</th>
                  <th class="py-4 px-6 eyebrow text-[0.65rem]">Format</th>
                  <th class="py-4 px-6 eyebrow text-[0.65rem]">Status</th>
                  <th class="py-4 px-6 eyebrow text-[0.65rem] text-right">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-border">
                <?php foreach ($events as $event): 
                  $is_upcoming = ($event['date'] ?? '') >= $today;
                  $is_pub = !empty($event['is_published']);
                ?>
                  <tr class="hover:bg-muted/30 transition-colors group">
                    <!-- Date Column -->
                    <td class="py-5 px-6 whitespace-nowrap align-top">
                      <div class="flex flex-col items-center justify-center rounded-xl border border-border bg-background px-3 py-2 w-14 text-center">
                        <span class="numeral text-2xl"><?php echo htmlspecialchars($event['day'] ?? '00', ENT_QUOTES, 'UTF-8'); ?></span>
                        <span class="eyebrow text-[0.55rem] tracking-wider"><?php echo htmlspecialchars($event['month'] ?? '---', ENT_QUOTES, 'UTF-8'); ?></span>
                      </div>
                      <div class="text-[0.7rem] text-muted-foreground mt-1.5 text-center">
                        <?php echo htmlspecialchars($event['date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                      </div>
                    </td>

                    <!-- Title & Details -->
                    <td class="py-5 px-6 align-top max-w-md">
                      <div class="flex items-start gap-4">
                        <div class="table-event-thumb">
                          <img src="<?php echo htmlspecialchars(get_asset_url($event['image'] ?? '', true), ENT_QUOTES, 'UTF-8'); ?>" alt="" class="photo-mono" />
                        </div>
                        <div class="min-w-0">
                          <h3 class="headline text-base font-bold text-foreground">
                            <?php echo htmlspecialchars($event['title'] ?? 'Untitled', ENT_QUOTES, 'UTF-8'); ?>
                          </h3>
                          <p class="text-xs text-muted-foreground line-clamp-2 mt-1">
                            <?php echo htmlspecialchars($event['summary'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                          </p>
                          <div class="flex items-center gap-3 text-xs text-muted-foreground mt-2">
                            <span>&#128337; <?php echo htmlspecialchars($event['time'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                            <span>&bull;</span>
                            <span>&#128205; <?php echo htmlspecialchars($event['location'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                          </div>
                        </div>
                      </div>
                    </td>

                    <!-- Category -->
                    <td class="py-5 px-6 align-top whitespace-nowrap">
                      <span class="inline-flex rounded-full bg-ink px-3 py-1 text-[0.65rem] font-semibold text-ink-foreground">
                        <?php echo htmlspecialchars($event['category'] ?? 'Session', ENT_QUOTES, 'UTF-8'); ?>
                      </span>
                    </td>

                    <!-- Format -->
                    <td class="py-5 px-6 align-top whitespace-nowrap text-xs font-medium">
                      <?php if (($event['format'] ?? '') === 'Online'): ?>
                        <span class="text-blue-600 font-semibold">&#128187; Online</span>
                      <?php else: ?>
                        <span class="text-foreground font-semibold">&#127970; Offline</span>
                      <?php endif; ?>
                    </td>

                    <!-- Status -->
                    <td class="py-5 px-6 align-top whitespace-nowrap">
                      <?php if ($is_pub): ?>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/10 px-3 py-1 text-[0.65rem] font-bold text-emerald-700">
                          <span class="size-1.5 rounded-full bg-emerald-600"></span> Published
                        </span>
                      <?php else: ?>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-muted px-3 py-1 text-[0.65rem] font-bold text-muted-foreground">
                          <span class="size-1.5 rounded-full bg-muted-foreground"></span> Draft
                        </span>
                      <?php endif; ?>
                    </td>

                    <!-- Actions -->
                    <td class="py-5 px-6 align-top text-right whitespace-nowrap">
                      <div class="flex items-center justify-end gap-2">
                        <a href="/events.php?slug=<?php echo urlencode($event['slug'] ?? ''); ?>" target="_blank" title="View Public Page" class="p-2 rounded-lg hover:bg-muted text-muted-foreground hover:text-foreground transition-colors">
                          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                            <polyline points="15 3 21 3 21 9"></polyline>
                            <line x1="10" y1="14" x2="21" y2="3"></line>
                          </svg>
                        </a>
                        <a href="/admin/event-edit.php?id=<?php echo urlencode($event['id']); ?>" class="inline-flex items-center gap-1 rounded-lg border border-border px-3 py-1.5 text-xs font-semibold hover:border-ink hover:bg-ink hover:text-ink-foreground transition-colors">
                          Edit
                        </a>
                        <form method="POST" action="/admin/event-delete.php" onsubmit="return confirm('Are you sure you want to delete this event? This action cannot be undone.');" class="inline-block">
                          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(get_csrf_token(), ENT_QUOTES, 'UTF-8'); ?>" />
                          <input type="hidden" name="id" value="<?php echo htmlspecialchars($event['id'], ENT_QUOTES, 'UTF-8'); ?>" />
                          <button type="submit" title="Delete Event" class="p-1.5 rounded-lg text-muted-foreground hover:text-brand hover:bg-brand/10 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                              <polyline points="3 6 5 6 21 6"></polyline>
                              <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            </svg>
                          </button>
                        </form>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>

    </div>
  </main>

  <footer class="border-t border-border/70 py-6 px-6 text-center text-xs text-muted-foreground">
    &copy; <?php echo date('Y'); ?> Xlator Content Management System &middot; Brand Swamy&reg;
  </footer>

</body>
</html>
