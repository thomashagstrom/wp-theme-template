const { test, expect, devices } = require('@playwright/test');
const { gotoPreview, getOverflowMetrics } = require('./utils/preview');

const pixel5 = devices['Pixel 5'];
const mobileUse = {
  viewport: pixel5.viewport,
  userAgent: pixel5.userAgent,
  deviceScaleFactor: pixel5.deviceScaleFactor,
  isMobile: pixel5.isMobile,
  hasTouch: pixel5.hasTouch
};

test.describe('Visual layout smoke checks', () => {
  test.use(mobileUse);

  test('desktop home preview keeps the hero content inside the viewport', async ({ page }) => {
    await gotoPreview(page, 'editorial-starter-homepage-editorial-layout.html');

    const viewportWidth = page.viewportSize().width;
    const heroParts = page.locator('.hero__content, .hero__visual');
    const count = await heroParts.count();
    expect(count).toBe(2);

    for (let index = 0; index < count; index += 1) {
      const part = heroParts.nth(index);
      const box = await part.boundingBox();
      expect(box).not.toBeNull();
      expect(box.x, `Hero section ${index} starts outside the viewport`).toBeGreaterThanOrEqual(-1);
      expect(box.x + box.width, `Hero section ${index} exceeds the viewport`).toBeLessThanOrEqual(viewportWidth + 1);
    }
  });

  test('home preview keeps headings and cards within viewport', async ({ page }, testInfo) => {
    await gotoPreview(page, 'editorial-starter-homepage-editorial-layout.html');

    const metrics = await getOverflowMetrics(page);
    expect(metrics.overflow).toBeLessThanOrEqual(1);

    const viewportWidth = page.viewportSize().width;
    const headings = page.locator('.section-heading');
    const headingCount = await headings.count();
    expect(headingCount).toBeGreaterThan(0);

    for (let index = 0; index < headingCount; index += 1) {
      const heading = headings.nth(index);
      if (!(await heading.isVisible())) {
        continue;
      }

      const box = await heading.boundingBox();
      if (!box) {
        continue;
      }

      expect(box.x).toBeGreaterThanOrEqual(-1);
      expect(box.x + box.width).toBeLessThanOrEqual(viewportWidth + 1);
    }

    const cards = page.locator('.card, .post-card');
    const cardCount = await cards.count();
    expect(cardCount).toBeGreaterThan(0);

    for (let index = 0; index < Math.min(cardCount, 3); index += 1) {
      const card = cards.nth(index);
      if (!(await card.isVisible())) {
        continue;
      }

      const box = await card.boundingBox();
      if (!box) {
        continue;
      }

      expect(box.x).toBeGreaterThanOrEqual(-1);
      expect(box.x + box.width).toBeLessThanOrEqual(viewportWidth + 1);
    }

    const screenshot = await page.screenshot({ fullPage: true });
    await testInfo.attach('home-mobile-layout', {
      body: screenshot,
      contentType: 'image/png'
    });
  });

  test('shopify preview keeps product grid and CTA aligned on mobile', async ({ page }, testInfo) => {
    await gotoPreview(page, 'editorial-starter-shopify-product-grid.html');

    const metrics = await getOverflowMetrics(page);
    expect(metrics.overflow).toBeLessThanOrEqual(1);

    const viewportWidth = page.viewportSize().width;
    const products = page.locator('.shopify-product');
    const productCount = await products.count();
    expect(productCount).toBeGreaterThan(0);

    for (let index = 0; index < Math.min(productCount, 3); index += 1) {
      const product = products.nth(index);
      const box = await product.boundingBox();
      if (!box) {
        continue;
      }

      expect(box.x).toBeGreaterThanOrEqual(-1);
      expect(box.x + box.width).toBeLessThanOrEqual(viewportWidth + 1);
    }

    const moreButton = page.locator('.shopify-products__more').first();
    await expect(moreButton).toBeVisible();
    const ctaBox = await moreButton.boundingBox();
    expect(ctaBox.x).toBeGreaterThanOrEqual(-1);
    expect(ctaBox.x + ctaBox.width).toBeLessThanOrEqual(viewportWidth + 1);

    const screenshot = await page.screenshot({ fullPage: true });
    await testInfo.attach('shopify-mobile-layout', {
      body: screenshot,
      contentType: 'image/png'
    });
  });
});
