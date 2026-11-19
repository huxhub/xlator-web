<?php
/**
 * Xlator - 404 Error Page
 * Branded Not Found Page for Hostinger
 */
require_once __DIR__ . '/includes/config.php';

http_response_code(404);

$page_title = "404 - Page Not Found — Xlator";
$page_description = "The page you are looking for doesn't exist or has been moved.";
$current_page = "404";

include __DIR__ . '/includes/header.php';
?>
  <main class="flex-1 flex items-center justify-center">
    <section class="x-motif overflow-hidden w-full py-24 px-5 text-center">
      <div class="mx-auto max-w-xl">
        <p class="numeral text-8xl md:text-9xl">404</p>
        <h1 class="headline mt-4 text-3xl md:text-4xl">Page Not Found</h1>
        <p class="mt-4 text-lg text-muted-foreground">
          The page you're looking for doesn't exist, has been removed, or has moved to a new address.
        </p>
        <div class="mt-8">
          <a class="inline-flex items-center justify-center gap-2 whitespace-nowrap cursor-pointer transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring rounded-full bg-brand text-brand-foreground uppercase tracking-wider font-semibold hover:bg-brand/90 shadow-[var(--shadow-lift)] h-12 px-8 text-sm" href="index.php">
            Return to Homepage
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-right size-4"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
          </a>
        </div>
      </div>
    </section>
  </main>

<?php include __DIR__ . '/includes/footer.php'; ?>
