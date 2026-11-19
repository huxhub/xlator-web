<?php
/**
 * Xlator Admin - Login Page
 */
require_once __DIR__ . '/../includes/config.php';

// Redirect if already logged in
if (is_admin_logged_in()) {
    header('Location: /admin/index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = strtolower(trim($_POST['username'] ?? ''));
    $password = trim($_POST['password'] ?? '');

    // Allow login if username is admin and password matches bcrypt hash or fallback password123
    $is_valid_user = ($username === strtolower(ADMIN_USERNAME));
    $is_valid_pass = password_verify($password, ADMIN_PASSWORD_HASH) || ($password === 'password123');

    if ($is_valid_user && $is_valid_pass) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user'] = ADMIN_USERNAME;
        set_flash_message('success', 'Welcome back! You are now logged in.');
        header('Location: /admin/index.php');
        exit;
    } else {
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Admin Login — Xlator</title>
  <link rel="icon" type="image/png" href="../images/favicon.png"/>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="anonymous"/>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600;700;800;900&family=Barlow+Condensed:wght@400;500;600&display=swap"/>
  <link rel="stylesheet" href="../css/styles.css"/>
  <link rel="stylesheet" href="../css/admin.css"/>
</head>
<body class="bg-background text-foreground min-h-screen flex flex-col justify-between selection:bg-brand selection:text-brand-foreground">

  <!-- Header minimal -->
  <header class="border-b border-border/70 bg-background/90 py-4 px-6 md:px-10">
    <div class="mx-auto max-w-7xl flex items-center justify-between">
      <a href="../index.php" class="h-10 inline-block">
        <img src="../images/logo-black.png" alt="Xlator" class="h-full w-auto" />
      </a>
      <a href="../index.php" class="eyebrow text-xs hover:text-brand transition-colors">
        &larr; Back to Live Site
      </a>
    </div>
  </header>

  <!-- Login Card -->
  <main class="flex-1 flex items-center justify-center p-6">
    <div class="w-full max-w-md rounded-3xl border border-border bg-card p-8 md:p-10 shadow-[var(--shadow-lift)]">
      <div class="text-center mb-8">
        <p class="eyebrow text-brand">Management Portal</p>
        <h1 class="headline mt-2 text-3xl md:text-4xl">Admin Login</h1>
        <p class="mt-2 text-sm text-muted-foreground">Sign in to manage events, sessions, and applications.</p>
      </div>

      <?php if (!empty($error)): ?>
        <div class="mb-6 rounded-xl border border-brand/40 bg-brand/10 p-4 text-sm text-brand font-medium">
          <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="/admin/login.php" class="space-y-5">
        <div>
          <label class="eyebrow text-[0.65rem] font-medium" for="username">Username</label>
          <input type="text" id="username" name="username" required autofocus autocomplete="username" class="flex w-full rounded-md border border-input bg-transparent px-3 py-1 text-base shadow-sm transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring md:text-sm mt-2 h-11" placeholder="admin" value="<?php echo htmlspecialchars($_POST['username'] ?? 'admin', ENT_QUOTES, 'UTF-8'); ?>"/>
        </div>

        <div>
          <label class="eyebrow text-[0.65rem] font-medium" for="password">Password</label>
          <div class="relative mt-2">
            <input type="password" id="password" name="password" required autocomplete="current-password" class="flex w-full rounded-md border border-input bg-transparent px-3 py-1 pr-10 text-base shadow-sm transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring md:text-sm h-11" placeholder="••••••••"/>
            <button type="button" id="togglePasswordBtn" onclick="togglePasswordVisibility()" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground text-xs font-medium cursor-pointer select-none">
              Show
            </button>
          </div>
        </div>

        <button type="submit" class="inline-flex w-full items-center justify-center gap-2 whitespace-nowrap cursor-pointer transition-colors rounded-full bg-brand text-brand-foreground uppercase tracking-wider font-semibold hover:bg-brand/90 shadow-[var(--shadow-lift)] h-12 px-7 text-sm mt-2">
          Sign In
        </button>
      </form>

      <!-- Default credentials hint with 1-click auto-fill -->
      <div class="mt-8 rounded-xl border border-border bg-muted/50 p-4 text-xs text-muted-foreground text-center">
        <p class="font-semibold text-foreground mb-1">Default Login Credentials</p>
        <p>Username: <code class="font-mono text-brand font-bold">admin</code></p>
        <p>Password: <code class="font-mono text-brand font-bold">password123</code></p>
        <div class="mt-3">
          <button type="button" onclick="fillCredentials()" class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-brand/10 text-brand font-semibold text-[0.75rem] hover:bg-brand hover:text-white transition-colors">
            ⚡ Auto-Fill Credentials
          </button>
        </div>
        <p class="mt-2 text-[0.7rem] opacity-75">Configurable in <code>includes/config.php</code></p>
      </div>
    </div>
  </main>

  <script>
    function togglePasswordVisibility() {
      const pass = document.getElementById('password');
      const btn = document.getElementById('togglePasswordBtn');
      if (pass.type === 'password') {
        pass.type = 'text';
        btn.textContent = 'Hide';
      } else {
        pass.type = 'password';
        btn.textContent = 'Show';
      }
    }

    function fillCredentials() {
      document.getElementById('username').value = 'admin';
      document.getElementById('password').value = 'password123';
      const pass = document.getElementById('password');
      pass.focus();
    }
  </script>

  <footer class="border-t border-border/70 py-4 px-6 text-center text-xs text-muted-foreground">
    &copy; <?php echo date('Y'); ?> Xlator Admin Panel &middot; Brand Swamy&reg;
  </footer>

</body>
</html>
