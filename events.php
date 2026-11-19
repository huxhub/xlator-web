<?php
/**
 * Xlator - Events & Programs Page (Dynamic)
 * Learn. Meet. Build together.
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/events-db.php';

// If slug is passed, display event detail
if (!empty($_GET['slug'])) {
    require __DIR__ . '/event-detail.php';
    exit;
}

$page_title = "Events & Programs — Xlator";
$page_description = "Upcoming brand-building masterclasses, open houses, and clinics with Brand Swamy & Faisal Cheeran.";
$current_page = "events";

$upcoming_events = get_upcoming_events();
$past_events = get_past_events();

include __DIR__ . '/includes/header.php';
?>
  <main class="flex-1">
    <!-- Events Hero -->
    <section class="x-motif x-motif-dark overflow-hidden bg-ink text-ink-foreground">
      <div class="mx-auto max-w-7xl px-5 py-16 md:px-10 md:py-24">
        <p class="eyebrow text-brand">Events &amp; programs</p>
        <h1 class="headline mt-4 max-w-3xl text-5xl md:text-7xl">Learn. Meet. <span class="text-brand">Build together.</span></h1>
        <p class="mt-6 max-w-xl text-lg text-ink-foreground/75">Offline sessions, masterclasses and programs with the Xlator mentors and community.</p>
      </div>
    </section>

    <!-- Events List & Tabs -->
    <section class="mx-auto max-w-7xl px-5 py-14 md:px-10 md:py-20">
      <div class="inline-flex rounded-full border border-border p-1" role="tablist">
        <button role="tab" aria-selected="true" class="eyebrow rounded-full px-6 py-2.5 transition-colors bg-ink text-ink-foreground cursor-pointer">
          upcoming (<?php echo count($upcoming_events); ?>)
        </button>
        <button role="tab" aria-selected="false" class="eyebrow rounded-full px-6 py-2.5 transition-colors text-muted-foreground hover:text-foreground cursor-pointer">
          past (<?php echo count($past_events); ?>)
        </button>
      </div>

      <!-- Upcoming Events Panel -->
      <div id="upcoming-events-panel" class="tab-panel">
        <?php if (empty($upcoming_events)): ?>
          <div class="mt-10 rounded-2xl border border-dashed border-border p-12 text-center">
            <p class="headline text-2xl text-muted-foreground">No upcoming events scheduled</p>
            <p class="mt-3 text-sm text-muted-foreground max-w-md mx-auto">New session dates will be announced soon. Check out our mentorship program or apply to join our next cohort.</p>
            <div class="mt-6">
              <a href="/join.php" class="inline-flex items-center justify-center rounded-full bg-brand px-6 py-3 text-xs uppercase tracking-wider font-semibold text-brand-foreground hover:bg-brand/90 transition-colors">
                Apply for Program
              </a>
            </div>
          </div>
        <?php else: ?>
          <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($upcoming_events as $event): 
              $detail_url = '/events.php?slug=' . urlencode($event['slug']);
            ?>
              <a class="group flex flex-col overflow-hidden rounded-2xl border border-border bg-card transition-all hover:-translate-y-1 hover:shadow-[var(--shadow-lift)]" href="<?php echo $detail_url; ?>">
                <div class="relative aspect-[16/10] overflow-hidden bg-muted">
                  <img src="<?php echo htmlspecialchars(get_asset_url($event['image'] ?? 'images/collaboration.jpg'), ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($event['title'], ENT_QUOTES, 'UTF-8'); ?>" loading="lazy" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105 photo-mono"/>
                  <div class="absolute left-4 top-4 flex flex-col items-center rounded-lg bg-background px-3 py-2 text-center">
                    <span class="numeral text-3xl"><?php echo htmlspecialchars($event['day'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <span class="eyebrow mt-0.5 text-[0.6rem] tracking-[0.2em]"><?php echo htmlspecialchars($event['month'], ENT_QUOTES, 'UTF-8'); ?></span>
                  </div>
                  <span class="eyebrow absolute right-4 top-4 rounded-full bg-ink px-3 py-1.5 text-[0.6rem] text-ink-foreground">
                    <?php echo htmlspecialchars($event['category'], ENT_QUOTES, 'UTF-8'); ?>
                  </span>
                </div>
                <div class="flex flex-1 flex-col p-6">
                  <h3 class="headline text-xl"><?php echo htmlspecialchars($event['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                  <p class="mt-3 line-clamp-3 text-sm text-muted-foreground"><?php echo htmlspecialchars($event['summary'], ENT_QUOTES, 'UTF-8'); ?></p>
                  <div class="mt-auto flex items-center justify-between pt-6 text-sm">
                    <span class="flex items-center gap-1.5 text-muted-foreground">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin size-4" aria-hidden="true"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path><circle cx="12" cy="10" r="3"></circle></svg>
                      <?php echo htmlspecialchars($event['location'], ENT_QUOTES, 'UTF-8'); ?> &middot; <?php echo htmlspecialchars(explode('–', $event['time'])[0] ?? $event['time'], ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-right size-5 text-brand transition-transform group-hover:translate-x-1" aria-hidden="true"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
                  </div>
                </div>
              </a>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <!-- Past Events Panel -->
      <div id="past-events-panel" class="tab-panel hidden">
        <?php if (empty($past_events)): ?>
          <div class="mt-10 rounded-2xl border border-dashed border-border p-12 text-center">
            <p class="headline text-2xl text-muted-foreground">No past events recorded</p>
            <p class="mt-3 text-sm text-muted-foreground max-w-md mx-auto">
              All of our current events are upcoming and open for registration above. Sign up to stay updated on future sessions!
            </p>
          </div>
        <?php else: ?>
          <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($past_events as $event): 
              $detail_url = '/events.php?slug=' . urlencode($event['slug']);
            ?>
              <a class="group flex flex-col overflow-hidden rounded-2xl border border-border bg-card opacity-85 transition-all hover:opacity-100 hover:-translate-y-1 hover:shadow-[var(--shadow-lift)]" href="<?php echo $detail_url; ?>">
                <div class="relative aspect-[16/10] overflow-hidden bg-muted">
                  <img src="<?php echo htmlspecialchars(get_asset_url($event['image'] ?? 'images/collaboration.jpg'), ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($event['title'], ENT_QUOTES, 'UTF-8'); ?>" loading="lazy" class="h-full w-full object-cover photo-mono"/>
                  <div class="absolute left-4 top-4 flex flex-col items-center rounded-lg bg-background px-3 py-2 text-center">
                    <span class="numeral text-3xl"><?php echo htmlspecialchars($event['day'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <span class="eyebrow mt-0.5 text-[0.6rem] tracking-[0.2em]"><?php echo htmlspecialchars($event['month'], ENT_QUOTES, 'UTF-8'); ?></span>
                  </div>
                  <span class="eyebrow absolute right-4 top-4 rounded-full bg-muted px-3 py-1.5 text-[0.6rem] text-muted-foreground">
                    Completed
                  </span>
                </div>
                <div class="flex flex-1 flex-col p-6">
                  <h3 class="headline text-xl"><?php echo htmlspecialchars($event['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                  <p class="mt-3 line-clamp-3 text-sm text-muted-foreground"><?php echo htmlspecialchars($event['summary'], ENT_QUOTES, 'UTF-8'); ?></p>
                  <div class="mt-auto flex items-center justify-between pt-6 text-sm">
                    <span class="text-xs text-muted-foreground">Session concluded</span>
                    <span class="text-xs font-semibold text-brand">View details &rarr;</span>
                  </div>
                </div>
              </a>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </section>
  </main>

<?php include __DIR__ . '/includes/footer.php'; ?>
