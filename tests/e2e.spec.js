import { test, expect } from '@playwright/test';

test.describe('Public Website End-to-End Tests', () => {

  test('Homepage loads properly with all key sections', async ({ page }) => {
    await page.goto('/');

    // Page title and branding
    await expect(page).toHaveTitle(/Xlator/);
    await expect(page.locator('header img[alt="Xlator"]')).toBeVisible();

    // Hero section
    await expect(page.locator('h1.headline')).toContainText("Brands aren't born");
    await expect(page.locator('a:has-text("Join the program")').first()).toBeVisible();

    // Stats section
    await expect(page.locator('text=Proven brand building techniques')).toBeVisible();

    // Events preview section
    await expect(page.locator('h2.headline:has-text("Events")')).toBeVisible();

    // Mentors section
    await expect(page.locator('p.headline:has-text("Brand Swamy")')).toBeVisible();
    await expect(page.locator('p.headline:has-text("Faisal Cheeran")')).toBeVisible();

    // Footer
    await expect(page.locator('footer')).toBeVisible();
    await expect(page.locator('footer a:has-text("Team login")')).toBeVisible();
  });

  test('Navbar links navigate to correct pages', async ({ page }) => {
    await page.goto('/');

    // Navigate to Program
    await page.click('header nav a:has-text("Program")');
    await expect(page).toHaveURL(/\/program/);
    await expect(page.locator('h1.headline')).toContainText('Purpose-driven brands');

    // Navigate to Mentors
    await page.click('header nav a:has-text("Mentors")');
    await expect(page).toHaveURL(/\/mentors/);
    await expect(page.locator('h1.headline')).toContainText('Two mentors');

    // Navigate to Events
    await page.click('header nav a:has-text("Events")');
    await expect(page).toHaveURL(/\/events/);
    await expect(page.locator('h1.headline')).toContainText('Learn. Meet. Build together.');

    // Navigate to Join
    await page.click('header nav a:has-text("Join Xlator")');
    await expect(page).toHaveURL(/\/join/);
    await expect(page.locator('h1.headline')).toContainText('Your brand can');
  });

  test('Events page tabs switch between Upcoming and Past events', async ({ page }) => {
    await page.goto('/events.php');

    const upcomingTab = page.locator('button[role="tab"]:has-text("upcoming")');
    const pastTab = page.locator('button[role="tab"]:has-text("past")');

    await expect(upcomingTab).toBeVisible();
    await expect(pastTab).toBeVisible();

    // Click Past tab
    await pastTab.click();
    await expect(pastTab).toHaveAttribute('aria-selected', 'true');
    await expect(upcomingTab).toHaveAttribute('aria-selected', 'false');

    // Click Upcoming tab back
    await upcomingTab.click();
    await expect(upcomingTab).toHaveAttribute('aria-selected', 'true');
    await expect(pastTab).toHaveAttribute('aria-selected', 'false');
  });

  test('Dynamic event detail page loads with event data', async ({ page }) => {
    await page.goto('/events.php');

    // Click on the first event card
    const firstEvent = page.locator('#upcoming-events-panel a.group').first();
    await expect(firstEvent).toBeVisible();
    await firstEvent.click();

    // Verify detail page elements
    await expect(page.locator('h1.headline')).toBeVisible();
    await expect(page.locator('text=Date')).toBeVisible();
    await expect(page.locator('text=Location')).toBeVisible();
    await expect(page.locator('a:has-text("Reserve my spot")')).toBeVisible();
    await expect(page.locator('a:has-text("All events")')).toBeVisible();
  });

  test('Join page form submits application successfully', async ({ page }) => {
    await page.goto('/join.php');

    // Select path
    const eliteBtn = page.locator('button[data-path="Xlator Elite"]');
    await eliteBtn.click();
    await expect(eliteBtn).toHaveClass(/selected/);

    // Fill form
    await page.fill('#name', 'Playwright Automated Tester');
    await page.fill('#email', 'playwright-test@example.com');
    await page.fill('#phone', '+91 99999 88888');
    await page.fill('#business_name', 'Automated QA Ltd');
    await page.fill('#message', 'Testing end-to-end form submission via Playwright.');

    // Submit
    await page.click('#submit-btn');

    // Verify success banner appears
    const successMsg = page.locator('#form-success-message');
    await expect(successMsg).toBeVisible({ timeout: 5000 });
    await expect(successMsg).toContainText('Application Received!');
  });
});

test.describe('Admin Route Handling & Authentication Tests', () => {

  test('Catch-all admin routes properly redirect to /admin/', async ({ page }) => {
    // Test mistyped or nested admin routes
    const response1 = await page.goto('/events.php/admin');
    await expect(page).toHaveURL(/\/admin\/?/);

    const response2 = await page.goto('/program.php/admin');
    await expect(page).toHaveURL(/\/admin\/?/);

    const response3 = await page.goto('/admin');
    await expect(page).toHaveURL(/\/admin\/?/);
  });

  test('Admin login rejects incorrect credentials', async ({ page }) => {
    await page.goto('/admin/login.php');

    await page.fill('#username', 'wronguser');
    await page.fill('#password', 'wrongpass');
    await page.click('button[type="submit"]');

    await expect(page.locator('text=Invalid username or password')).toBeVisible();
  });

  test('Admin login accepts valid credentials and opens dashboard', async ({ page }) => {
    await page.goto('/admin/login.php');

    // Fill configured credentials (xlator / password123)
    await page.fill('#username', 'xlator');
    await page.fill('#password', 'password123');
    await page.click('button[type="submit"]');

    // Should successfully redirect to dashboard
    await expect(page).toHaveURL(/\/admin\/index\.php/);
    await expect(page.locator('h1.headline')).toContainText('Events & Programs');
    await expect(page.locator('text=Logged in as')).toBeVisible();
    await expect(page.locator('a:has-text("Add New Event")')).toBeVisible();

    // Verify table has events listed
    await expect(page.locator('table')).toBeVisible();
    await expect(page.locator('table tbody tr')).not.toHaveCount(0);
  });

  test('Admin can open event editor, fill details, and cancel back to dashboard', async ({ page }) => {
    // Log in
    await page.goto('/admin/login.php');
    await page.fill('#username', 'xlator');
    await page.fill('#password', 'password123');
    await page.click('button[type="submit"]');
    await expect(page).toHaveURL(/\/admin\/index\.php/);

    // Click Add New Event
    await page.click('a:has-text("Add New Event")');
    await expect(page).toHaveURL(/\/admin\/event-edit\.php/);

    // Verify form elements exist
    await expect(page.locator('#title')).toBeVisible();
    await expect(page.locator('#date')).toBeVisible();
    await expect(page.locator('#category')).toBeVisible();
    await expect(page.locator('#format')).toBeVisible();
    await expect(page.locator('#masterImagePreview')).toBeVisible();

    // Click cancel
    await page.click('a:has-text("Cancel")');
    await expect(page).toHaveURL(/\/admin\/index\.php/);
  });

  test('Admin logout destroys session and redirects to login', async ({ page }) => {
    // Log in
    await page.goto('/admin/login.php');
    await page.fill('#username', 'xlator');
    await page.fill('#password', 'password123');
    await page.click('button[type="submit"]');
    await expect(page).toHaveURL(/\/admin\/index\.php/);

    // Click Log Out
    await page.click('a:has-text("Log Out")');
    await expect(page).toHaveURL(/\/admin\/login\.php/);

    // Attempting to visit /admin/index.php should redirect back to login
    await page.goto('/admin/index.php');
    await expect(page).toHaveURL(/\/admin\/login\.php/);
  });
});
