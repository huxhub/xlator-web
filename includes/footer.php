<?php
/**
 * Xlator - Footer Component
 * Modular PHP footer for Hostinger
 */

$is_nested = $is_nested ?? false;
$prefix = $is_nested ? '../' : '';
?>
    <footer class="x-motif x-motif-dark overflow-hidden bg-ink text-ink-foreground">
      <div class="mx-auto grid max-w-7xl gap-12 px-5 py-16 md:grid-cols-[1.2fr_1fr_1fr] md:px-10">
        <div>
          <a aria-label="Xlator home" class="inline-block shrink-0 h-20" href="<?php echo $prefix; ?>index.php">
            <img src="<?php echo $prefix; ?>images/logo-white.png" alt="Xlator" class="h-full w-auto" width="783" height="889"/>
          </a>
          <p class="mt-6 max-w-xs text-sm text-ink-foreground/70">A brand building mentorship programme for business owners. Presented by Brand Swamy.</p>
        </div>
        <div>
          <p class="eyebrow text-ink-foreground/50">Explore</p>
          <ul class="mt-4 space-y-2 text-sm">
            <li><a class="hover:text-brand transition-colors" href="<?php echo $prefix; ?>program.php">The Program</a></li>
            <li><a class="hover:text-brand transition-colors" href="<?php echo $prefix; ?>mentors.php">Mentors</a></li>
            <li><a class="hover:text-brand transition-colors" href="<?php echo $prefix; ?>events.php">Events &amp; Programs</a></li>
            <li><a class="hover:text-brand transition-colors" href="<?php echo $prefix; ?>join.php">Join Xlator</a></li>
          </ul>
        </div>
        <div>
          <p class="eyebrow text-ink-foreground/50">Build it. Grow it. Scale it.</p>
          <p class="headline mt-4 text-2xl">Brands aren't born.<br/><span class="text-brand">They're built together.</span></p>
        </div>
      </div>
      <div class="border-t border-ink-foreground/10">
        <div class="mx-auto flex max-w-7xl flex-col justify-between gap-2 px-5 py-6 text-xs text-ink-foreground/50 md:flex-row md:px-10">
          <span>&copy; <?php echo date('Y'); ?> Xlator &middot; Brand Swamy&reg;</span>
          <a class="hover:text-ink-foreground transition-colors cursor-pointer" href="<?php echo $prefix; ?>admin/index.php">Team login</a>
        </div>
      </div>
    </footer>
  </div>
  <script src="<?php echo $prefix; ?>js/main.js"></script>
</body>
</html>
