<?php
/**
 * Xlator - Mentors Page
 * Two mentors. Years of brand-building experience.
 */
require_once __DIR__ . '/includes/config.php';

$page_title = "Mentors — Xlator";
$page_description = "Learn directly from Brand Swamy and Faisal Cheeran — years of strategic thinking and creative execution.";
$current_page = "mentors";

include __DIR__ . '/includes/header.php';
?>
  <main class="flex-1">
    <section class="mx-auto max-w-7xl px-5 pt-16 md:px-10 md:pt-24">
      <p class="eyebrow text-brand">Meet your mentors</p>
      <h1 class="headline mt-4 max-w-3xl text-5xl md:text-6xl">Two mentors. Years of brand-building experience.</h1>
    </section>

    <!-- Mentor 1: Brand Swamy -->
    <section class="mx-auto max-w-7xl px-5 py-16 md:px-10 md:py-20">
      <div class="grid items-center gap-12 md:grid-cols-2">
        <img src="/images/mentor-brand-swamy.jpg" alt="Portrait of Brand Swamy" loading="eager" class="aspect-[4/5] w-full rounded-3xl object-cover object-top shadow-[var(--shadow-lift)]"/>
        <div>
          <p class="text-3xl font-medium tracking-wide text-foreground/80">Brand Swamy®</p>
          <h2 class="headline mt-6 text-4xl md:text-5xl">
            <span class="block">Strategic thinking</span>
            <span class="block">&amp; business mindset</span>
          </h2>
          <ul class="mt-8 space-y-2 text-lg">
            <li class="flex gap-3"><span class="text-brand">•</span>20+ years in advertising</li>
            <li class="flex gap-3"><span class="text-brand">•</span>100+ brands launched</li>
            <li class="flex gap-3"><span class="text-brand">•</span>300+ products worked on</li>
            <li class="flex gap-3"><span class="text-brand">•</span>Strategic thinking · Business mindset · Marketing strategy</li>
          </ul>
        </div>
      </div>
    </section>

    <!-- Mentor 2: Faisal Cheeran -->
    <section class="mx-auto max-w-7xl px-5 py-16 md:px-10 md:py-20">
      <div class="grid items-center gap-12 md:grid-cols-2 md:[&>*:first-child]:order-last">
        <img src="/images/mentor-faisal-cheeran.jpg" alt="Portrait of Faisal Cheeran" loading="lazy" class="aspect-[4/5] w-full rounded-3xl object-cover object-top shadow-[var(--shadow-lift)]"/>
        <div>
          <p class="text-3xl font-medium tracking-wide text-foreground/80">Faisal Cheeran®</p>
          <h2 class="headline mt-6 text-4xl md:text-5xl">
            <span class="block">Creative thinking.</span>
            <span class="block">Business experience.</span>
            <span class="block text-brand">Real-world execution.</span>
          </h2>
          <ul class="mt-8 space-y-2 text-lg">
            <li class="flex gap-3"><span class="text-brand">•</span>20+ years of business &amp; creative experience</li>
            <li class="flex gap-3"><span class="text-brand">•</span>Business integration · Creative strategy</li>
            <li class="flex gap-3"><span class="text-brand">•</span>Real-world execution · Customer experience</li>
            <li class="flex gap-3"><span class="text-brand">•</span>Marketing &amp; growth</li>
          </ul>
          <div class="mt-8 grid gap-3 sm:grid-cols-2">
            <div class="rounded-sm bg-muted p-5 font-semibold text-brand">2,500,000+ social media community</div>
            <div class="rounded-sm bg-muted p-5 font-semibold text-brand">1,000,000+ customers</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Learn in Person Banner -->
    <section class="bg-ink text-ink-foreground">
      <div class="mx-auto flex max-w-7xl flex-col items-start justify-between gap-6 px-5 py-14 md:flex-row md:items-center md:px-10">
        <h2 class="headline text-3xl md:text-4xl">Learn from them in person.</h2>
        <div class="flex flex-wrap gap-3">
          <a class="inline-flex items-center justify-center gap-2 whitespace-nowrap cursor-pointer transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 disabled:cursor-not-allowed [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 rounded-full border-2 border-ink-foreground bg-transparent text-ink-foreground uppercase tracking-wider font-semibold hover:bg-ink-foreground hover:text-ink h-12 px-7 text-sm" href="/events.php">
            See upcoming sessions
          </a>
          <a class="inline-flex items-center justify-center gap-2 whitespace-nowrap cursor-pointer transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 disabled:cursor-not-allowed [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 rounded-full bg-brand text-brand-foreground uppercase tracking-wider font-semibold hover:bg-brand/90 shadow-[var(--shadow-lift)] h-12 px-7 text-sm" href="/join.php">
            Join Xlator 
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-right" aria-hidden="true"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
          </a>
        </div>
      </div>
    </section>
  </main>

<?php include __DIR__ . '/includes/footer.php'; ?>
