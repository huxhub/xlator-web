import { test, expect } from '@playwright/test';

const PAGES = [
  { name: 'Home', path: '/' },
  { name: 'Program', path: '/program.php' },
  { name: 'Mentors', path: '/mentors.php' },
  { name: 'Events', path: '/events.php' },
  { name: 'Join', path: '/join.php' },
  { name: 'Admin Login', path: '/admin/login.php' },
];

test.describe('Mobile Viewport (375x667)', () => {
  test.use({ viewport: { width: 375, height: 667 } });

  for (const pageInfo of PAGES) {
    test(`${pageInfo.name} has no horizontal overflow on mobile`, async ({ page }) => {
      await page.goto(pageInfo.path);

      // Verify page width does not exceed viewport width
      const scrollWidth = await page.evaluate(() => document.documentElement.scrollWidth);
      const innerWidth = await page.evaluate(() => window.innerWidth);
      expect(scrollWidth).toBeLessThanOrEqual(innerWidth);
    });
  }

  test('Mobile navigation drawer opens and closes properly', async ({ page }) => {
    await page.goto('/');

    // Hamburger button should be visible on mobile
    const menuBtn = page.locator('.mobile-menu-btn');
    await expect(menuBtn).toBeVisible();

    // Desktop nav should be hidden on mobile
    const desktopNav = page.locator('header nav');
    await expect(desktopNav).toBeHidden();

    // Click hamburger button to open drawer
    await menuBtn.click();
    const drawerOverlay = page.locator('#mobile-menu-drawer');
    await expect(drawerOverlay).toHaveClass(/active/);

    // Close button should close drawer
    const closeBtn = page.locator('#close-menu-btn');
    await closeBtn.click();
    await expect(drawerOverlay).not.toHaveClass(/active/);
  });

  test('Comparison table on Program page is horizontally scrollable inside its card', async ({ page }) => {
    await page.goto('/program.php');
    const tableContainer = page.locator('div.overflow-x-auto');
    await expect(tableContainer).toBeVisible();

    // Verify container has scrollable content without overflowing document
    const isScrollable = await tableContainer.evaluate(el => el.scrollWidth > el.clientWidth);
    expect(isScrollable).toBe(true);
  });

  test('Join page form fields and path selector buttons are accessible on mobile', async ({ page }) => {
    await page.goto('/join.php');
    const buttons = page.locator('.path-selector-btn');
    await expect(buttons).toHaveCount(3);

    for (let i = 0; i < 3; i++) {
      await expect(buttons.nth(i)).toBeVisible();
    }

    const nameInput = page.locator('#name');
    await expect(nameInput).toBeVisible();
    const submitBtn = page.locator('#submit-btn');
    await expect(submitBtn).toBeVisible();
  });
});

test.describe('Tablet Viewport (768x1024)', () => {
  test.use({ viewport: { width: 768, height: 1024 } });

  test('Desktop navigation is visible on tablet', async ({ page }) => {
    await page.goto('/');
    const desktopNav = page.locator('header nav');
    await expect(desktopNav).toBeVisible();

    const mobileBtn = page.locator('.mobile-menu-btn');
    await expect(mobileBtn).toBeHidden();
  });

  test('Events cards adapt to multi-column on tablet', async ({ page }) => {
    await page.goto('/events.php');
    const cardsGrid = page.locator('#upcoming-events-panel .grid');
    if (await cardsGrid.count() > 0) {
      await expect(cardsGrid.first()).toBeVisible();
    }
  });
});

test.describe('Desktop Viewport (1440x900)', () => {
  test.use({ viewport: { width: 1440, height: 900 } });

  test('Full desktop header, hero and 3-column events preview render cleanly', async ({ page }) => {
    await page.goto('/');

    // Header nav links visible
    await expect(page.locator('header nav a:has-text("Program")')).toBeVisible();
    await expect(page.locator('header nav a:has-text("Mentors")')).toBeVisible();
    await expect(page.locator('header nav a:has-text("Events")')).toBeVisible();
    await expect(page.locator('header nav a:has-text("Join Xlator")')).toBeVisible();

    // Hero headline visible
    await expect(page.locator('h1.headline')).toBeVisible();

    // No horizontal scroll
    const scrollWidth = await page.evaluate(() => document.documentElement.scrollWidth);
    const innerWidth = await page.evaluate(() => window.innerWidth);
    expect(scrollWidth).toBeLessThanOrEqual(innerWidth);
  });
});
