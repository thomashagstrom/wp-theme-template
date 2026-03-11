const { test, expect, devices } = require('@playwright/test');

const baseUrl = process.env.BASE_URL || 'https://example.com';

const pixel5 = devices['Pixel 5'];
const mobileUse = {
  viewport: pixel5.viewport,
  userAgent: pixel5.userAgent,
  deviceScaleFactor: pixel5.deviceScaleFactor,
  isMobile: pixel5.isMobile,
  hasTouch: pixel5.hasTouch
};

async function dismissOverlays(page) {
  const closeSelectors = [
    "button[aria-label*='Close' i]",
    "button[aria-label*='Stäng' i]",
    '.jetpack-subscribe-modal__close',
    '.jetpack-subscribe-overlay__close',
    '.slidedown-button'
  ];

  for (const selector of closeSelectors) {
    const control = page.locator(selector).first();
    if ((await control.count()) === 0) {
      continue;
    }

    try {
      await control.click({ timeout: 1200 });
    } catch {
      // Keep this helper best-effort, because third-party overlays vary between runs.
    }
  }

  try {
    await page.keyboard.press('Escape');
  } catch {
    // Ignore missing focus targets.
  }
}

async function expectNoHorizontalOverflow(page) {
  const { clientWidth, scrollWidth } = await page.evaluate(() => {
    const root = document.documentElement;
    return {
      clientWidth: root.clientWidth,
      scrollWidth: root.scrollWidth
    };
  });

  expect(
    scrollWidth - clientWidth,
    `Horizontal overflow detected at ${baseUrl}: scrollWidth=${scrollWidth}, clientWidth=${clientWidth}`
  ).toBeLessThanOrEqual(1);
}

test.describe('Live site visual smoke', () => {
  test('desktop home can render without horizontal overflow', async ({ page }, testInfo) => {
    await page.goto(baseUrl, { waitUntil: 'networkidle' });
    await dismissOverlays(page);

    await expect(page.locator('body')).toBeVisible();
    await expectNoHorizontalOverflow(page);

    const screenshot = await page.screenshot({ fullPage: true });
    await testInfo.attach('desktop-live-home', {
      body: screenshot,
      contentType: 'image/png'
    });
  });

  test.describe('mobile', () => {
    test.use(mobileUse);

    test('mobile home keeps CTA and layout inside viewport', async ({ page }, testInfo) => {
      await page.goto(baseUrl, { waitUntil: 'networkidle' });
      await dismissOverlays(page);

      await expectNoHorizontalOverflow(page);

      const viewportWidth = page.viewportSize().width;
      const ctas = page.locator('.button, .wp-block-button__link, .shopify-products__more, .shopify-product__cta');
      const ctaCount = await ctas.count();

      for (let index = 0; index < ctaCount; index += 1) {
        const cta = ctas.nth(index);
        if (!(await cta.isVisible())) {
          continue;
        }

        const box = await cta.boundingBox();
        if (!box) {
          continue;
        }

        expect(box.x, `CTA starts before viewport (index ${index}) at ${baseUrl}`).toBeGreaterThanOrEqual(-1);
        expect(box.x + box.width, `CTA exceeds viewport width (index ${index}) at ${baseUrl}`).toBeLessThanOrEqual(
          viewportWidth + 1
        );
      }

      const screenshot = await page.screenshot({ fullPage: true });
      await testInfo.attach('mobile-live-home', {
        body: screenshot,
        contentType: 'image/png'
      });
    });
  });
});
