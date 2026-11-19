<?php
/** Admin view of applications submitted through the Join form. */
require_once __DIR__ . '/../includes/config.php';
require_admin_login();
header('Cache-Control: private, no-store');
require_once __DIR__ . '/../includes/applications-db.php';

$applications = get_all_applications();
$flash = get_flash_message();

/**
 * @param mixed $value
 * @return string
 */
function application_text(mixed $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Applications — Xlator Admin</title>
  <base href="/"/>
  <link rel="icon" type="image/png" href="/images/favicon.png"/>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600;700;800;900&family=Barlow+Condensed:wght@400;500;600&display=swap"/>
  <link rel="stylesheet" href="/css/styles.css"/>
  <link rel="stylesheet" href="/css/admin.css"/>
</head>
<body class="bg-background text-foreground min-h-screen flex flex-col">
  <header class="sticky top-0 z-40 border-b border-border/70 bg-card/90 backdrop-blur">
    <div class="mx-auto flex h-20 max-w-7xl items-center justify-between px-5 md:px-10">
      <a href="/admin/index.php" class="h-10 inline-block" aria-label="Xlator admin home">
        <img src="/images/logo-black.png" alt="Xlator" class="h-full w-auto"/>
      </a>
      <nav class="admin-applications-nav" aria-label="Admin navigation">
        <a href="/admin/index.php" class="rounded-full border border-border px-4 py-2 text-xs font-semibold hover:bg-muted transition-colors">Events</a>
        <a href="/admin/logout.php" class="rounded-full border border-border px-4 py-2 text-xs font-semibold hover:bg-muted transition-colors">Log Out</a>
      </nav>
    </div>
  </header>

  <main class="flex-1 py-10 px-5 md:px-10">
    <div class="mx-auto max-w-7xl">
      <?php if ($flash): ?>
        <div class="admin-flash <?php echo $flash['type'] === 'success' ? 'admin-flash-success' : 'admin-flash-error'; ?>" role="status">
          <?php echo application_text($flash['text']); ?>
        </div>
      <?php endif; ?>
      <div class="pb-8 border-b border-border">
        <p class="eyebrow text-brand">Join Xlator</p>
        <h1 class="headline mt-2 text-4xl md:text-5xl">Applications</h1>
        <p class="mt-2 text-muted-foreground">
          <?php echo count($applications); ?> <?php echo count($applications) === 1 ? 'application' : 'applications'; ?> received through the Join form.
        </p>
      </div>

      <?php if (empty($applications)): ?>
        <div class="admin-applications-empty rounded-3xl border border-border bg-card">
          <h2 class="headline text-2xl">No applications yet</h2>
          <p class="mt-2 text-sm text-muted-foreground">New Join form submissions will appear here automatically.</p>
        </div>
      <?php else: ?>
        <div class="admin-applications-list">
          <?php foreach ($applications as $application): ?>
            <?php
              $email = trim((string)($application['email'] ?? ''));
              $phone = trim((string)($application['phone'] ?? ''));
              $phone_href = preg_replace('/[^+0-9]/', '', $phone);
            ?>
            <article class="admin-application-card rounded-3xl border border-border bg-card shadow-[var(--shadow-lift)]">
              <div class="admin-application-heading">
                <div class="min-w-0">
                  <p class="eyebrow text-brand">New lead</p>
                  <h2 class="headline text-2xl"><?php echo application_text($application['name'] ?? ''); ?></h2>
                </div>
                <div class="admin-application-meta">
                  <span class="admin-application-path"><?php echo application_text($application['path'] ?? 'Undecided'); ?></span>
                  <time><?php echo application_text($application['timestamp'] ?? ''); ?></time>
                </div>
              </div>

              <dl class="admin-application-details">
                <div>
                  <dt>Email</dt>
                  <dd>
                    <?php if (filter_var($email, FILTER_VALIDATE_EMAIL)): ?>
                      <a href="mailto:<?php echo application_text($email); ?>"><?php echo application_text($email); ?></a>
                    <?php else: ?>
                      <?php echo application_text($email ?: '—'); ?>
                    <?php endif; ?>
                  </dd>
                </div>
                <div>
                  <dt>Phone / WhatsApp</dt>
                  <dd>
                    <?php if ($phone_href !== ''): ?>
                      <a href="tel:<?php echo application_text($phone_href); ?>"><?php echo application_text($phone); ?></a>
                    <?php else: ?>
                      —
                    <?php endif; ?>
                  </dd>
                </div>
                <div>
                  <dt>Business</dt>
                  <dd><?php echo application_text(($application['business_name'] ?? '') ?: 'Not provided'); ?></dd>
                </div>
              </dl>

              <div class="admin-application-message">
                <h3>About the brand</h3>
                <p><?php echo application_text(($application['message'] ?? '') ?: 'No message provided.'); ?></p>
              </div>

              <div class="admin-application-actions">
                <?php if (filter_var($email, FILTER_VALIDATE_EMAIL)): ?>
                  <a class="admin-application-reply" href="mailto:<?php echo application_text($email); ?>?subject=<?php echo rawurlencode('Re: Your Brand Xlator application'); ?>">Reply by email</a>
                <?php endif; ?>
                <form method="post" action="/admin/application-delete.php" onsubmit="return confirm('Delete this application permanently? This cannot be undone.');">
                  <input type="hidden" name="csrf_token" value="<?php echo application_text(get_csrf_token()); ?>"/>
                  <input type="hidden" name="id" value="<?php echo application_text($application['id']); ?>"/>
                  <button type="submit" class="admin-application-delete">Delete</button>
                </form>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </main>

  <footer class="border-t border-border/70 py-6 px-6 text-center text-xs text-muted-foreground">
    &copy; <?php echo date('Y'); ?> Xlator Content Management System &middot; Brand Swamy&reg;
  </footer>
</body>
</html>
