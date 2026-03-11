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

const previews = [
  'editorial-starter-homepage-editorial-layout.html',
  'editorial-starter-shopify-product-grid.html'
];

test.describe('Mobile viewport containment', () => {
  test.use(mobileUse);

  for (const previewFile of previews) {
    test(`${previewFile} stays within the viewport`, async ({ page }) => {
      await gotoPreview(page, previewFile);

      const metrics = await getOverflowMetrics(page);
      expect(
        metrics.overflow,
        `Expected no horizontal overflow for ${previewFile}. scrollWidth=${metrics.scrollWidth}, clientWidth=${metrics.clientWidth}`
      ).toBeLessThanOrEqual(1);

      const width = page.viewportSize().width;
      const ctas = page.locator('.button, .wp-block-button__link, .shopify-products__more, .shopify-product__cta');
      const count = await ctas.count();

      for (let index = 0; index < count; index += 1) {
        const button = ctas.nth(index);
        if (!(await button.isVisible())) {
          continue;
        }

        const box = await button.boundingBox();
        if (!box) {
          continue;
        }

        expect(box.x, `CTA starts outside viewport in ${previewFile} at index ${index}`).toBeGreaterThanOrEqual(-1);
        expect(
          box.x + box.width,
          `CTA overflows right edge in ${previewFile} at index ${index}`
        ).toBeLessThanOrEqual(width + 1);
      }
    });
  }
});
