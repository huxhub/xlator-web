<?php
/**
 * Xlator - Header Component
 * Modular PHP header for Hostinger
 */

if (!defined('SITE_NAME')) {
    require_once __DIR__ . '/config.php';
}

$page_title = $page_title ?? (SITE_NAME . ' — ' . SITE_TAGLINE);
$page_description = $page_description ?? "Xlator by Brand Swamy is a brand-building mentorship programme for business owners: 100+ hours, offline sessions and a complete brand framework.";
$current_page = $current_page ?? 'home';
$is_nested = $is_nested ?? false;
$prefix = $is_nested ? '../' : '';

// Active classes
$prog_cls = ($current_page === 'program') ? 'text-brand font-bold' : 'text-foreground/70';
$ment_cls = ($current_page === 'mentors') ? 'text-brand font-bold' : 'text-foreground/70';
$evnt_cls = ($current_page === 'events') ? 'text-brand font-bold' : 'text-foreground/70';
$join_btn_cls = ($current_page === 'join') ? 'bg-brand text-brand-foreground' : 'bg-ink text-ink-foreground hover:bg-ink-soft';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title><?php echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?></title>
  <meta name="description" content="<?php echo htmlspecialchars($page_description, ENT_QUOTES, 'UTF-8'); ?>"/>
  <meta name="author" content="Xlator"/>
  <meta property="og:title" content="<?php echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?>"/>
  <meta property="og:description" content="<?php echo htmlspecialchars($page_description, ENT_QUOTES, 'UTF-8'); ?>"/>
  <meta property="og:type" content="website"/>
  <base href="/"/>
  <link rel="icon" type="image/png" href="/images/favicon.png"/>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="anonymous"/>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600;700;800;900&family=Barlow+Condensed:wght@400;500;600&display=swap"/>
  <link rel="stylesheet" href="/css/styles.css"/>
</head>
<body class="bg-background text-foreground antialiased selection:bg-brand selection:text-brand-foreground">
  <div class="flex min-h-screen flex-col">
    <header class="sticky top-0 z-40 border-b border-border/70 bg-background/90 backdrop-blur">
      <div class="mx-auto flex h-20 max-w-7xl items-center justify-between px-5 md:px-10">
        <a aria-label="Xlator home" class="inline-block shrink-0 h-14" href="/index.php">
          <img src="/images/logo-black.png" alt="Xlator" class="h-full w-auto" width="783" height="889"/>
        </a>
        <nav class="hidden items-center gap-10 md:flex">
          <a class="eyebrow <?php echo $prog_cls; ?> transition-colors hover:text-foreground" href="/program.php">Program</a>
          <a class="eyebrow <?php echo $ment_cls; ?> transition-colors hover:text-foreground" href="/mentors.php">Mentors</a>
          <a class="eyebrow <?php echo $evnt_cls; ?> transition-colors hover:text-foreground" href="/events.php">Events</a>
          <a class="inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm cursor-pointer transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 disabled:cursor-not-allowed [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 <?php echo $join_btn_cls; ?> uppercase tracking-wider font-semibold h-10 rounded-md px-8" href="/join.php">Join Xlator</a>
        </nav>
        <button class="md:hidden mobile-menu-btn cursor-pointer p-2" aria-label="Open menu">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-menu size-7" aria-hidden="true">
            <path d="M4 5h16"></path><path d="M4 12h16"></path><path d="M4 19h16"></path>
          </svg>
        </button>
      </div>
    </header>

    <!-- Mobile Navigation Drawer -->
    <div id="mobile-menu-drawer" class="mobile-drawer-overlay" aria-hidden="true">
      <div class="mobile-drawer" role="dialog" aria-modal="true" aria-label="Mobile Navigation">
        <div class="flex items-center justify-between p-6 border-b border-border">
          <a href="/index.php" class="h-10 inline-block">
            <img src="/images/logo-black.png" alt="Xlator" class="h-full w-auto" />
          </a>
          <button id="close-menu-btn" class="p-2 rounded-lg hover:bg-muted text-foreground transition-colors cursor-pointer" aria-label="Close menu">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-6">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </button>
        </div>
        <nav class="flex flex-col p-6 space-y-6">
          <a class="eyebrow text-base tracking-widest hover:text-brand transition-colors <?php echo ($current_page === 'program') ? 'text-brand font-bold' : ''; ?>" href="/program.php">Program</a>
          <a class="eyebrow text-base tracking-widest hover:text-brand transition-colors <?php echo ($current_page === 'mentors') ? 'text-brand font-bold' : ''; ?>" href="/mentors.php">Mentors</a>
          <a class="eyebrow text-base tracking-widest hover:text-brand transition-colors <?php echo ($current_page === 'events') ? 'text-brand font-bold' : ''; ?>" href="/events.php">Events</a>
          <div class="pt-4">
            <a class="inline-flex w-full items-center justify-center h-12 rounded-full bg-brand text-brand-foreground uppercase tracking-wider font-semibold hover:bg-brand/90 transition-colors text-sm" href="/join.php">
              Join Xlator
            </a>
          </div>
        </nav>
      </div>
    </div>
