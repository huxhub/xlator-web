/**
 * Xlator - Pure JavaScript Interactivity
 * Zero dependencies, pure vanilla JS
 */

document.addEventListener('DOMContentLoaded', () => {
  // 1. Mobile Menu Drawer Toggle
  const openMenuBtn = document.querySelector('.mobile-menu-btn');
  const closeMenuBtn = document.querySelector('#close-menu-btn');
  const drawerOverlay = document.querySelector('#mobile-menu-drawer');
  const drawer = document.querySelector('.mobile-drawer');

  function openMenu() {
    if (drawerOverlay) {
      drawerOverlay.classList.add('active');
      drawerOverlay.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';
    }
  }

  function closeMenu() {
    if (drawerOverlay) {
      drawerOverlay.classList.remove('active');
      drawerOverlay.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = '';
    }
  }

  if (openMenuBtn) {
    openMenuBtn.addEventListener('click', openMenu);
  }

  if (closeMenuBtn) {
    closeMenuBtn.addEventListener('click', closeMenu);
  }

  if (drawerOverlay) {
    drawerOverlay.addEventListener('click', (e) => {
      if (e.target === drawerOverlay) {
        closeMenu();
      }
    });
  }

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && drawerOverlay && drawerOverlay.classList.contains('active')) {
      closeMenu();
    }
  });

  // 2. Join Page - Interactive Path Selector
  const pathButtons = document.querySelectorAll('.path-selector-btn');
  const pathInput = document.querySelector('#selected-path-input');

  pathButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      pathButtons.forEach(b => {
        b.classList.remove('selected', 'border-ink', 'bg-ink', 'text-ink-foreground');
        b.classList.add('border-border');
        b.setAttribute('aria-pressed', 'false');
      });
      btn.classList.add('selected', 'border-ink', 'bg-ink', 'text-ink-foreground');
      btn.classList.remove('border-border');
      btn.setAttribute('aria-pressed', 'true');
      
      const chosenPath = btn.getAttribute('data-path') || btn.textContent.trim();
      if (pathInput) {
        pathInput.value = chosenPath;
      }
    });
  });

  // 3. Join Page - Form Submission to PHP backend
  const joinForm = document.querySelector('#join-form');
  const formSuccess = document.querySelector('#form-success-message');
  const submitBtn = document.querySelector('#submit-btn');

  if (joinForm) {
    joinForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      
      const name = document.querySelector('#name')?.value.trim();
      const email = document.querySelector('#email')?.value.trim();
      
      if (!name || !email) {
        alert('Please fill in your name and email address.');
        return;
      }

      // Visual loading state
      const originalBtnText = submitBtn ? submitBtn.innerHTML : 'Submit Application';
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = 'Submitting application...';
      }

      try {
        const formData = new FormData(joinForm);
        const endpoint = joinForm.getAttribute('action') || '/api/submit-application.php';
        
        const response = await fetch(endpoint, {
          method: 'POST',
          body: formData
        });

        const data = await response.json();

        if (response.ok && data.success) {
          if (formSuccess) {
            if (data.notification_sent === false) {
              const copy = document.querySelector('#application-success-copy');
              if (copy) {
                copy.textContent = 'Your application was saved, but the email notification could not be sent. Please contact the team directly';
                if (data.contact_email) {
                  const link = document.createElement('a');
                  link.href = `mailto:${data.contact_email}`;
                  link.textContent = ` at ${data.contact_email}`;
                  link.className = 'underline font-semibold';
                  copy.append(link);
                }
                copy.append('.');
              }
            }
            joinForm.style.display = 'none';
            formSuccess.classList.remove('hidden');
            formSuccess.scrollIntoView({ behavior: 'smooth', block: 'center' });
          } else {
            alert(data.message || 'Thank you! Your application has been received.');
            joinForm.reset();
          }
        } else {
          alert(data.error || 'There was a problem submitting your application. Please try again.');
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
          }
        }
      } catch (err) {
        console.warn('AJAX submit error, falling back:', err);
        // Fallback to standard form submission if fetch blocked
        joinForm.submit();
      }
    });
  }

  // 4. Events Page - Tabs Filtering (Upcoming / Past)
  const eventTabs = document.querySelectorAll('[role="tab"]');
  const upcomingPanel = document.querySelector('#upcoming-events-panel');
  const pastPanel = document.querySelector('#past-events-panel');

  eventTabs.forEach(tab => {
    tab.addEventListener('click', () => {
      eventTabs.forEach(t => {
        t.setAttribute('aria-selected', 'false');
        t.classList.remove('bg-ink', 'text-ink-foreground');
        t.classList.add('text-muted-foreground');
      });

      tab.setAttribute('aria-selected', 'true');
      tab.classList.add('bg-ink', 'text-ink-foreground');
      tab.classList.remove('text-muted-foreground');

      const isUpcoming = tab.textContent.toLowerCase().includes('upcoming');
      if (upcomingPanel && pastPanel) {
        if (isUpcoming) {
          upcomingPanel.classList.remove('hidden');
          pastPanel.classList.add('hidden');
        } else {
          upcomingPanel.classList.add('hidden');
          pastPanel.classList.remove('hidden');
        }
      }
    });
  });

  // 5. Header Scroll Effect
  const header = document.querySelector('header');
  window.addEventListener('scroll', () => {
    if (window.scrollY > 20) {
      header?.classList.add('shadow-sm');
    } else {
      header?.classList.remove('shadow-sm');
    }
  });
});
