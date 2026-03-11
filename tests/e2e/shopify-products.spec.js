const path = require('node:path');
const fs = require('node:fs');
const { test, expect } = require('@playwright/test');

const shopifyScript = fs.readFileSync(path.resolve(__dirname, '../../assets/js/shopify-products.js'), 'utf8');

const containerMarkup = `
  <section class="shopify-products"
    data-shopify-products
    data-shopify-shop="shop.example.com"
    data-shopify-limit="2"
    data-shopify-locale="sv-SE"
    data-shopify-currency="SEK"
    data-shopify-cta-label="Visa produkt"
    data-shopify-fallback-url="https://shop.example.com/fallback"
    data-shopify-empty-title="Kunde inte hämta produkter"
    data-shopify-empty-cta="Till butiken">
    <div class="shopify-products__grid">
      <article class="card shopify-product shopify-product--placeholder">
        <div class="shopify-product__content"><p>Loading...</p></div>
      </article>
    </div>
    <p class="shopify-products__error" hidden>Error</p>
  </section>
`;

test.describe('Shopify product business behavior', () => {
  test('renders product cards from Shopify JSON feed', async ({ page }) => {
    await page.setContent(containerMarkup);

    await page.evaluate(() => {
      window.fetch = async () => {
        return {
          ok: true,
          status: 200,
          json: async () => ({
            products: [
              {
                title: 'Starter Notebook',
                handle: 'starter-notebook',
                body_html: '<p>Editorial merch item one.</p>',
                images: [{ src: 'https://shop.example.com/image-a.jpg' }],
                variants: [{ price: '129.00' }]
              },
              {
                title: 'Field Guide',
                handle: 'field-guide',
                body_html: '<p>Editorial merch item two.</p>',
                images: [{ src: 'https://shop.example.com/image-b.jpg' }],
                variants: [{ price: '149.00' }]
              }
            ]
          })
        };
      };
    });

    await page.addScriptTag({ content: shopifyScript });

    await expect(page.locator('.shopify-product__title')).toHaveCount(2);
    await expect(page.locator('.shopify-product__title').first()).toContainText('Starter Notebook');
    await expect(page.locator('.shopify-product__cta').first()).toHaveAttribute(
      'href',
      'https://shop.example.com/products/starter-notebook'
    );
    await expect(page.locator('.shopify-products__error')).toBeHidden();
    await expect(page.locator('.shopify-product--empty')).toHaveCount(0);
  });

  test('shows fallback empty-state card when Shopify feed cannot be loaded', async ({ page }) => {
    await page.setContent(containerMarkup);

    await page.evaluate(() => {
      window.fetch = async () => {
        return {
          ok: false,
          status: 500,
          json: async () => ({})
        };
      };
    });

    await page.addScriptTag({ content: shopifyScript });

    const error = page.locator('.shopify-products__error');
    await expect(error).toBeVisible();

    const emptyCard = page.locator('.shopify-product--empty');
    await expect(emptyCard).toBeVisible();
    await expect(emptyCard.locator('.shopify-product__title')).toHaveText('Kunde inte hämta produkter');
    await expect(emptyCard.locator('.shopify-product__cta')).toHaveAttribute(
      'href',
      'https://shop.example.com/fallback'
    );
    await expect(emptyCard.locator('.shopify-product__cta')).toHaveText('Till butiken');
  });
});
