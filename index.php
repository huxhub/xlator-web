<?php
/**
 * Xlator - Home Page
 * Brands Aren't Born. They're Built Together.
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/events-db.php';

$page_title = "Xlator — Brands Aren't Born. They're Built Together.";
$page_description = "Xlator by Brand Swamy is a brand-building mentorship programme for business owners: 100+ hours, offline sessions and a complete brand framework.";
$current_page = "home";

$preview_events = get_upcoming_events(3);

include __DIR__ . '/includes/header.php';
?>
  <main class="flex-1">
    <!-- Hero Section -->
    <section class="x-motif overflow-hidden">
      <div class="mx-auto grid max-w-7xl items-center gap-10 px-5 py-16 md:grid-cols-[1.1fr_1fr] md:px-10 md:py-24">
        <div class="animate-rise">
          <p class="eyebrow text-foreground/70">Brand Swamy presents</p>
          <img src="/images/logo-black.png" alt="" aria-hidden="true" class="mt-8 h-28 w-auto md:h-36"/>
          <h1 class="headline mt-10 text-5xl md:text-7xl">Brands aren't born.<br/><span class="text-brand">They're built together.</span></h1>
          <p class="mt-6 max-w-md text-lg text-muted-foreground">A brand building mentorship programme for business owners.</p>
          <div class="mt-10 flex flex-wrap gap-3">
            <a class="inline-flex items-center justify-center gap-2 whitespace-nowrap cursor-pointer transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 disabled:cursor-not-allowed [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 rounded-full bg-brand text-brand-foreground uppercase tracking-wider font-semibold hover:bg-brand/90 shadow-[var(--shadow-lift)] h-12 px-7 text-sm" href="/join.php">
              Join the program 
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-right" aria-hidden="true"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
            </a>
            <a class="inline-flex items-center justify-center gap-2 whitespace-nowrap cursor-pointer transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 disabled:cursor-not-allowed [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 rounded-full border-2 border-current bg-transparent uppercase tracking-wider font-semibold hover:bg-foreground hover:text-background h-12 px-7 text-sm" href="/events.php">
              Upcoming events
            </a>
          </div>
        </div>
        <div class="relative mx-auto w-full max-w-md md:max-w-none">
          <img src="/images/hero-handshake.jpg" alt="Business owner climbing steps toward a helping hand" class="photo-mono aspect-[4/5] w-full rounded-3xl object-cover shadow-[var(--shadow-lift)]"/>
        </div>
      </div>
    </section>

    <!-- 7 Pillars Dark Section -->
    <section class="x-motif x-motif-dark overflow-hidden bg-ink text-ink-foreground">
      <div class="mx-auto grid max-w-7xl items-center gap-14 px-5 py-20 md:grid-cols-2 md:px-10 md:py-28">
        <div>
          <ul class="space-y-1 text-3xl md:text-4xl">
            <li class="uppercase"><span class="font-light">Right </span><span class="font-extrabold">People.</span></li>
            <li class="uppercase"><span class="font-light">Right </span><span class="font-extrabold">Attitude.</span></li>
            <li class="uppercase"><span class="font-light">Right </span><span class="font-extrabold">Culture.</span></li>
            <li class="uppercase"><span class="font-light">Right </span><span class="font-extrabold">Method.</span></li>
            <li class="uppercase"><span class="font-light">Right </span><span class="font-extrabold">Process.</span></li>
            <li class="uppercase"><span class="font-light">Right </span><span class="font-extrabold">Strategy.</span></li>
            <li class="uppercase"><span class="font-light">Right </span><span class="font-extrabold">Action.</span></li>
          </ul>
          <p class="mt-10 max-w-sm text-ink-foreground/80">That's how the right brand is built. Because brands aren't born. They're built together.</p>
          <p class="mt-4 text-xl font-light">Welcome to Xlator.</p>
        </div>
        <img src="images/team-together.jpg" alt="Team collaborating around laptops" loading="lazy" class="photo-mono w-full rounded-3xl object-cover"/>
      </div>
    </section>

    <!-- Numbers Counter Section -->
    <section class="x-motif overflow-hidden">
      <div class="mx-auto max-w-7xl px-5 py-20 md:px-10 md:py-28">
        <h2 class="headline max-w-2xl text-4xl md:text-5xl">More than a program.<br/>A brand-building journey.</h2>
        <div class="mt-12 grid grid-cols-2 gap-3 md:grid-cols-5">
          <div class="rounded-sm bg-muted px-5 py-8 text-center">
            <p class="numeral text-6xl md:text-7xl">100+</p>
            <p class="mt-4 text-sm">Hours of structured learning</p>
          </div>
          <div class="rounded-sm bg-muted px-5 py-8 text-center">
            <p class="numeral text-6xl md:text-7xl">04</p>
            <p class="mt-4 text-sm">Exclusive offline sessions</p>
          </div>
          <div class="rounded-sm bg-muted px-5 py-8 text-center">
            <p class="numeral text-6xl md:text-7xl">200+</p>
            <p class="mt-4 text-sm">Brand builders &amp; creators</p>
          </div>
          <div class="rounded-sm bg-muted px-5 py-8 text-center">
            <p class="numeral text-6xl md:text-7xl">100+</p>
            <p class="mt-4 text-sm">Proven brand building techniques</p>
          </div>
          <div class="rounded-sm bg-muted px-5 py-8 text-center">
            <p class="numeral text-6xl md:text-7xl">01</p>
            <p class="mt-4 text-sm">Complete brand building framework</p>
          </div>
        </div>
        <a class="inline-flex items-center justify-center gap-2 whitespace-nowrap cursor-pointer transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 disabled:cursor-not-allowed [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 rounded-full border-2 border-current bg-transparent uppercase tracking-wider font-semibold hover:bg-foreground hover:text-background h-12 px-7 text-sm mt-12" href="/program.php">
          Explore the program 
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-right" aria-hidden="true"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
        </a>
      </div>
    </section>

    <!-- Upcoming Events Preview (Dynamic) -->
    <section class="border-y border-border bg-card">
      <div class="mx-auto max-w-7xl px-5 py-20 md:px-10 md:py-24">
        <div class="flex flex-wrap items-end justify-between gap-6">
          <div>
            <p class="eyebrow text-brand">What's next</p>
            <h2 class="headline mt-3 text-4xl md:text-5xl">Events &amp; programs</h2>
          </div>
          <a class="eyebrow flex items-center gap-2 hover:text-brand" href="/events.php">
            All events 
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-right size-4" aria-hidden="true"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
          </a>
        </div>

        <div class="mt-12 grid gap-6 md:grid-cols-3">
          <?php foreach ($preview_events as $event): 
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
      </div>
    </section>

    <!-- Mentors Section -->
    <section class="mx-auto max-w-7xl px-5 py-20 md:px-10 md:py-28">
      <p class="eyebrow text-brand">Two mentors. Years of experience.</p>
      <h2 class="headline mt-3 text-4xl md:text-5xl">Now on your team.</h2>
      <div class="mt-12 grid gap-6 md:grid-cols-2">
        <a class="group relative overflow-hidden rounded-3xl bg-ink text-ink-foreground" href="/mentors.php">
          <img src="/images/mentor-brand-swamy.jpg" alt="Brand Swamy®" loading="lazy" class="aspect-[4/5] w-full object-cover object-top transition-transform duration-700 group-hover:scale-105 md:aspect-[5/6]"/>
          <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-ink via-ink/70 to-transparent p-8 pt-24">
            <p class="headline text-3xl">Brand Swamy®</p>
            <p class="mt-2 text-sm uppercase tracking-wider text-ink-foreground/80">Strategic thinking &amp; business mindset</p>
          </div>
        </a>
        <a class="group relative overflow-hidden rounded-3xl bg-ink text-ink-foreground" href="/mentors.php">
          <img src="/images/mentor-faisal-cheeran.jpg" alt="Faisal Cheeran®" loading="lazy" class="aspect-[4/5] w-full object-cover object-top transition-transform duration-700 group-hover:scale-105 md:aspect-[5/6]"/>
          <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-ink via-ink/70 to-transparent p-8 pt-24">
            <p class="headline text-3xl">Faisal Cheeran®</p>
            <p class="mt-2 text-sm uppercase tracking-wider text-ink-foreground/80">Creative thinking &amp; real-world execution</p>
          </div>
        </a>
      </div>
    </section>

    <!-- CTA Banner -->
    <section class="bg-brand text-brand-foreground">
      <div class="mx-auto flex max-w-7xl flex-col items-start justify-between gap-8 px-5 py-16 md:flex-row md:items-center md:px-10">
        <div>
          <p class="eyebrow opacity-80">Build it. Grow it. Scale it.</p>
          <h2 class="headline mt-3 text-4xl md:text-5xl">Your brand can go further.</h2>
        </div>
        <a class="inline-flex items-center justify-center gap-2 whitespace-nowrap cursor-pointer transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 disabled:cursor-not-allowed [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 rounded-full bg-ink text-ink-foreground uppercase tracking-wider font-semibold hover:bg-ink-soft h-12 px-7 text-sm" href="/join.php">
          Join Brand Xlator now 
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-right" aria-hidden="true"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
        </a>
      </div>
    </section>
  </main>

<?php include __DIR__ . '/includes/footer.php'; ?>
