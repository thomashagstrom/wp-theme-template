const path = require('node:path');
const fs = require('node:fs');
const { test, expect } = require('@playwright/test');

const themeScript = fs.readFileSync(path.resolve(__dirname, '../../assets/js/theme.js'), 'utf8');

test.describe('Business analytics events', () => {
  test('tracks CTA and subscription funnel events with expected placements', async ({ page }) => {
    await page.setContent(`
      <main class="site-main">
        <a id="primary-cta" href="#" data-cta-type="primary" data-cta-placement="hero">Read the featured story</a>
        <a id="newsletter-cta" href="#" data-cta-type="newsletter" data-cta-placement="inline-page">Join the newsletter</a>

        <section class="newsletter">
          <div class="wp-block-jetpack-subscriptions">
            <form id="newsletter-form">
              <input type="email" name="email" value="reader@example.com" />
              <button id="newsletter-button" type="button">Subscribe</button>
            </form>
          </div>
        </section>
      </main>
    `);

    await page.evaluate(() => {
      window.dataLayer = [];
      window.__gtagCalls = [];
      window.gtag = (...args) => {
        window.__gtagCalls.push(args);
      };
      window.editorialStarterNav = {
        expand: 'Expand submenu',
        collapse: 'Collapse submenu'
      };
    });

    await page.addScriptTag({ content: themeScript });

    await page.click('#primary-cta');
    await page.click('#newsletter-cta');
    await page.click('#newsletter-button');
    await page.evaluate(() => {
      const form = document.querySelector('#newsletter-form');
      form.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
    });

    await page.evaluate(() => {
      const container = document.querySelector('.wp-block-jetpack-subscriptions');
      const success = document.createElement('div');
      success.className = 'wp-block-jetpack-subscriptions__success';
      success.textContent = 'Subscribed';
      container.appendChild(success);

      const duplicate = document.createElement('div');
      duplicate.className = 'wp-block-jetpack-subscriptions__success';
      duplicate.textContent = 'Subscribed again';
      container.appendChild(duplicate);
    });

    await expect.poll(async () => {
      return page.evaluate(() =>
        window.dataLayer.filter((entry) => entry.event === 'newsletter_success').length
      );
    }).toBe(1);

    const events = await page.evaluate(() =>
      window.dataLayer.map((entry) => ({
        event: entry.event,
        placement: entry.placement
      }))
    );

    expect(events).toEqual(
      expect.arrayContaining([
        { event: 'primary_cta_click', placement: 'hero' },
        { event: 'newsletter_cta_click', placement: 'inline-page' },
        { event: 'newsletter_submit', placement: 'newsletter_inline' },
        { event: 'newsletter_cta_click', placement: 'newsletter_inline' },
        { event: 'newsletter_success', placement: 'newsletter_inline' }
      ])
    );

    const gtagEventNames = await page.evaluate(() =>
      window.__gtagCalls
        .filter((args) => args[0] === 'event')
        .map((args) => args[1])
    );

    expect(gtagEventNames).toEqual(
      expect.arrayContaining([
        'primary_cta_click',
        'newsletter_cta_click',
        'newsletter_submit',
        'newsletter_success'
      ])
    );
  });
});
