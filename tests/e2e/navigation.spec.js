const path = require('node:path');
const fs = require('node:fs');
const { test, expect } = require('@playwright/test');

const themeScript = fs.readFileSync(path.resolve(__dirname, '../../assets/js/theme.js'), 'utf8');

test.describe('Navigation behavior', () => {
  test('submenu toggles are injected, labeled, and keyboard-close correctly', async ({ page }) => {
    await page.setContent(`
      <header>
        <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">Menu</button>
        <nav class="primary-navigation" aria-label="Primary menu">
          <ul id="primary-menu">
            <li id="parent-a" class="menu-item menu-item-has-children">
              <a href="#alpha">Alpha</a>
              <ul class="sub-menu">
                <li class="menu-item"><a href="#alpha-child">Alpha Child</a></li>
              </ul>
            </li>
            <li id="parent-b" class="menu-item menu-item-has-children">
              <a href="#beta">Beta</a>
              <ul class="sub-menu">
                <li class="menu-item"><a href="#beta-child">Beta Child</a></li>
              </ul>
            </li>
          </ul>
        </nav>
      </header>
    `);

    await page.evaluate(() => {
      window.editorialStarterNav = {
        expand: 'Expand submenu',
        collapse: 'Collapse submenu'
      };
    });

    await page.addScriptTag({ content: themeScript });

    const alphaToggle = page.locator('#parent-a > .submenu-toggle');
    const betaToggle = page.locator('#parent-b > .submenu-toggle');

    await expect(alphaToggle).toBeVisible();
    await expect(betaToggle).toBeVisible();

    await expect(alphaToggle).toHaveAttribute('aria-expanded', 'false');
    await expect(alphaToggle.locator('.screen-reader-text')).toHaveText('Expand submenu Alpha');

    await alphaToggle.click();
    await expect(alphaToggle).toHaveAttribute('aria-expanded', 'true');
    await expect(page.locator('#parent-a')).toHaveClass(/is-open/);
    await expect(alphaToggle.locator('.screen-reader-text')).toHaveText('Collapse submenu Alpha');

    await betaToggle.click();
    await expect(page.locator('#parent-b')).toHaveClass(/is-open/);
    await expect(page.locator('#parent-a')).not.toHaveClass(/is-open/);

    await page.locator('#parent-b .sub-menu a').focus();
    await page.keyboard.press('Escape');

    await expect(page.locator('#parent-b')).not.toHaveClass(/is-open/);
    await expect
      .poll(async () =>
        page.evaluate(() => document.activeElement && document.activeElement.classList.contains('submenu-toggle'))
      )
      .toBe(true);
  });

  test('mobile menu toggle closes open submenu tree when menu closes', async ({ page }) => {
    await page.setContent(`
      <header>
        <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">Menu</button>
        <nav class="primary-navigation" aria-label="Primary menu">
          <ul id="primary-menu">
            <li id="parent-a" class="menu-item menu-item-has-children">
              <a href="#alpha">Alpha</a>
              <ul class="sub-menu">
                <li class="menu-item"><a href="#alpha-child">Alpha Child</a></li>
              </ul>
            </li>
          </ul>
        </nav>
      </header>
    `);

    await page.evaluate(() => {
      window.editorialStarterNav = {
        expand: 'Expand submenu',
        collapse: 'Collapse submenu'
      };
    });

    await page.addScriptTag({ content: themeScript });

    const menuToggle = page.locator('.menu-toggle');
    const alphaToggle = page.locator('#parent-a > .submenu-toggle');

    await menuToggle.click();
    await expect(menuToggle).toHaveAttribute('aria-expanded', 'true');
    await expect(page.locator('.primary-navigation')).toHaveClass(/is-open/);

    await alphaToggle.click();
    await expect(page.locator('#parent-a')).toHaveClass(/is-open/);

    await menuToggle.click();
    await expect(menuToggle).toHaveAttribute('aria-expanded', 'false');
    await expect(page.locator('.primary-navigation')).not.toHaveClass(/is-open/);
    await expect(page.locator('#parent-a')).not.toHaveClass(/is-open/);
    await expect(alphaToggle).toHaveAttribute('aria-expanded', 'false');
  });
});
