<?php
/**
 * Xlator - Join / Application Page
 * Your brand can go further.
 */
require_once __DIR__ . '/includes/config.php';

$page_title = "Join Xlator — Apply Now";
$page_description = "Take your brand to the next level with Brand Xlator mentorship. Apply today.";
$current_page = "join";

$selected_plan = isset($_GET['plan']) ? trim($_GET['plan']) : (isset($_GET['event']) ? 'Xlator' : 'Undecided');
if (!in_array($selected_plan, ['Xlator', 'Xlator Elite', 'Undecided'], true)) {
    $selected_plan = 'Undecided';
}

include __DIR__ . '/includes/header.php';
?>
  <main class="flex-1">
    <section class="x-motif overflow-hidden">
      <div class="mx-auto grid max-w-7xl gap-14 px-5 py-16 md:grid-cols-[1fr_1.1fr] md:px-10 md:py-24">
        <div>
          <p class="eyebrow text-brand">Join Brand Xlator</p>
          <h1 class="headline mt-4 text-5xl md:text-6xl">Your brand can<br/><span class="text-brand">go further.</span></h1>
          <p class="mt-6 max-w-md text-lg text-muted-foreground">Tell us about your business and we'll get in touch with the next steps. Grow your brand to the next level. Every day.</p>
          <img src="/images/business-presentation.jpg" alt="Mentor presenting a business development plan" class="photo-mono mt-10 hidden w-full rounded-3xl object-cover md:block"/>
        </div>

        <div class="rounded-3xl border border-border bg-card p-6 shadow-[var(--shadow-lift)] md:p-10">
          <form id="join-form" action="/api/submit-application.php" method="POST" class="space-y-5">
            <input type="hidden" name="path" id="selected-path-input" value="<?php echo htmlspecialchars($selected_plan, ENT_QUOTES, 'UTF-8'); ?>" />
            
            <!-- Honeypot anti-spam field (hidden from real users) -->
            <input type="text" name="website_url" style="display:none !important;" tabindex="-1" autocomplete="off" />

            <!-- Path Selector -->
            <div>
              <label class="font-medium peer-disabled:cursor-not-allowed peer-disabled:opacity-70 eyebrow text-[0.65rem]">Choose your path</label>
              <div class="mt-2 grid grid-cols-3 gap-2">
                <button type="button" data-path="Xlator" aria-pressed="<?php echo ($selected_plan === 'Xlator') ? 'true' : 'false'; ?>" class="path-selector-btn rounded-lg border-2 px-2 py-3 text-xs sm:text-sm font-semibold transition-colors text-center flex items-center justify-center <?php echo ($selected_plan === 'Xlator') ? 'border-ink bg-ink text-ink-foreground selected' : 'border-border hover:border-ink'; ?>">
                  Xlator
                </button>
                <button type="button" data-path="Xlator Elite" aria-pressed="<?php echo ($selected_plan === 'Xlator Elite') ? 'true' : 'false'; ?>" class="path-selector-btn rounded-lg border-2 px-2 py-3 text-xs sm:text-sm font-semibold transition-colors text-center flex items-center justify-center <?php echo ($selected_plan === 'Xlator Elite') ? 'border-ink bg-ink text-ink-foreground selected' : 'border-border hover:border-ink'; ?>">
                  Xlator Elite
                </button>
                <button type="button" data-path="Undecided" aria-pressed="<?php echo ($selected_plan === 'Undecided') ? 'true' : 'false'; ?>" class="path-selector-btn rounded-lg border-2 px-2 py-3 text-xs sm:text-sm font-semibold transition-colors text-center flex items-center justify-center <?php echo ($selected_plan === 'Undecided') ? 'border-ink bg-ink text-ink-foreground selected' : 'border-border hover:border-ink'; ?>">
                  Undecided
                </button>
              </div>
            </div>

            <!-- Full Name -->
            <div>
              <label class="font-medium peer-disabled:cursor-not-allowed peer-disabled:opacity-70 eyebrow text-[0.65rem]" for="name">Full name *</label>
              <input type="text" class="flex w-full rounded-md border border-input bg-transparent px-3 py-1 text-base shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 md:text-sm mt-2 h-11" id="name" name="name" required autocomplete="name" placeholder="John Doe"/>
            </div>

            <!-- Email & Phone -->
            <div class="grid gap-5 sm:grid-cols-2">
              <div>
                <label class="font-medium peer-disabled:cursor-not-allowed peer-disabled:opacity-70 eyebrow text-[0.65rem]" for="email">Email *</label>
                <input type="email" class="flex w-full rounded-md border border-input bg-transparent px-3 py-1 text-base shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 md:text-sm mt-2 h-11" id="email" name="email" required autocomplete="email" placeholder="john@example.com"/>
              </div>
              <div>
                <label class="font-medium peer-disabled:cursor-not-allowed peer-disabled:opacity-70 eyebrow text-[0.65rem]" for="phone">Phone / WhatsApp</label>
                <input type="tel" class="flex w-full rounded-md border border-input bg-transparent px-3 py-1 text-base shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 md:text-sm mt-2 h-11" id="phone" name="phone" autocomplete="tel" placeholder="+91 98765 43210"/>
              </div>
            </div>

            <!-- Business Name -->
            <div>
              <label class="font-medium peer-disabled:cursor-not-allowed peer-disabled:opacity-70 eyebrow text-[0.65rem]" for="business_name">Business name</label>
              <input type="text" class="flex w-full rounded-md border border-input bg-transparent px-3 py-1 text-base shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 md:text-sm mt-2 h-11" id="business_name" name="business_name" autocomplete="organization" placeholder="Your Brand / Enterprise"/>
            </div>

            <!-- Brand Message -->
            <div>
              <label class="font-medium peer-disabled:cursor-not-allowed peer-disabled:opacity-70 eyebrow text-[0.65rem]" for="message">Tell us about your brand</label>
              <textarea class="flex min-h-[60px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-base shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 md:text-sm mt-2" id="message" name="message" rows="4" maxlength="2000" placeholder="Briefly describe your company, goals, and what you aim to achieve with Xlator mentorship..."></textarea>
            </div>

            <!-- Submit Button -->
            <button id="submit-btn" class="inline-flex items-center justify-center gap-2 whitespace-nowrap cursor-pointer transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 disabled:cursor-not-allowed [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 rounded-full bg-brand text-brand-foreground uppercase tracking-wider font-semibold hover:bg-brand/90 shadow-[var(--shadow-lift)] h-12 px-7 text-sm w-full" type="submit">
              Submit Application
            </button>
          </form>

          <!-- Success Message State -->
          <div id="form-success-message" class="hidden rounded-2xl border border-brand/30 bg-brand/5 p-8 text-center animate-rise">
            <div class="mx-auto flex size-14 items-center justify-center rounded-full bg-brand text-brand-foreground mb-4">
              <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 6 9 17l-5-5"/>
              </svg>
            </div>
            <h3 class="headline text-2xl md:text-3xl">Application Received!</h3>
            <p class="mt-3 text-muted-foreground max-w-md mx-auto">
              Thank you for choosing Brand Xlator. Our mentorship team will review your application and contact you within 2 business days.
            </p>
            <div class="mt-6">
              <a href="/index.php" class="inline-flex items-center justify-center rounded-full bg-ink px-6 py-3 text-xs uppercase tracking-wider font-semibold text-ink-foreground hover:bg-ink-soft transition-colors">
                Return to Home
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

<?php include __DIR__ . '/includes/footer.php'; ?>
