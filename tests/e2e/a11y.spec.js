const { test, expect, devices } = require('@playwright/test');
const AxeBuilder = require('@axe-core/playwright').default;
const { gotoPreview } = require('./utils/preview');

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

function summarizeViolations(violations) {
  return violations
    .map((violation) => {
      const targets = violation.nodes
        .flatMap((node) => node.target)
        .slice(0, 3)
        .join(', ');
      return `${violation.id} (${violation.impact}): ${targets}`;
    })
    .join('\n');
}

test.describe('Accessibility smoke checks', () => {
  test.use(mobileUse);

  for (const previewFile of previews) {
    test(`${previewFile} has no critical or serious WCAG A violations`, async ({ page }) => {
      await gotoPreview(page, previewFile);

      const results = await new AxeBuilder({ page })
        .withTags(['wcag2a'])
        .analyze();

      const blocking = results.violations.filter(
        (violation) => violation.impact === 'critical' || violation.impact === 'serious'
      );

      expect(
        blocking,
        `Accessibility violations found in ${previewFile}:\n${summarizeViolations(blocking)}`
      ).toEqual([]);
    });
  }
});
