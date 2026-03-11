# Editorial Starter

Editorial Starter is a reusable WordPress theme template for content-led sites, launch pages, and lightweight commerce experiences. It includes reusable block patterns, a neutral starter visual system, Playwright regression coverage, PHPCS checks, and release packaging.

## Bootstrap A New Theme

1. Copy this repository into `wp-content/themes/<your-theme-slug>`.
2. Rename the directory so it exactly matches the slug you want to ship.
3. Run the initializer:

```bash
npm run init-theme -- --theme-name="Acme Journal" --theme-slug="acme-journal" --site-url="https://example.com"
```

Optional flags:

```bash
--contact-email="team@example.com"
--shop-domain="shop.example.com"
```

The initializer rewrites the theme name, slug, text domain, PHP identifiers, package metadata, placeholder URLs, contact email, Shopify placeholder domain, `llms*.txt`, and the `.pot` filename. It will fail if the current directory name does not match `--theme-slug`.

## What Ships In The Starter

- Classic PHP WordPress theme structure with support for the block editor, template parts, menus, widgets, and featured images.
- Reusable block patterns for a homepage layout, hero, featured stories grid, newsletter signup, footer signoff, and optional Shopify product grid.
- Theme Customizer controls for hero content, a global primary CTA, SEO description, accent color, and footer social links.
- SEO helpers for canonical tags, Open Graph, Twitter cards, WebSite schema, Organization schema, Article schema, and breadcrumbs.
- Playwright smoke tests for preview layouts, accessibility, navigation behavior, analytics events, Shopify behavior, and optional live-site visual checks.
- Release tooling for changelog generation, preview generation, ZIP packaging, and CI verification.

## Directory Guide

- `style.css`: WordPress theme metadata and global design tokens.
- `functions.php`: Theme setup, asset loading, shared template helpers, and CTA utilities.
- `inc/customizer.php`: Customizer controls and footer social-link management.
- `inc/patterns.php`: Pattern category registration and pattern bootstrapping.
- `inc/seo.php`: Meta tags and schema output.
- `patterns/`: Starter block patterns.
- `assets/css/theme.css`: Component-level styling used by patterns and templates.
- `assets/js/theme.js`: Navigation behavior, CTA analytics hooks, reveal animation helpers, and optional hero enhancements.
- `tests/`: Playwright coverage plus initializer tests.
- `scripts/init-theme.mjs`: One-pass project bootstrap script.

## Development

Requirements:

- PHP 8.1+
- Node.js 22.x / npm 10+
- WordPress 6.5+ recommended

Install dependencies:

```bash
npm install
composer install
npx playwright install chromium
```

Quality gates:

- `npm run lint`
- `npm run ts`
- `npm run test:dev`
- `npm run verify`

`npm run verify` runs initializer tests plus local preview/browser coverage. It does not run the live-site visual suite by default because that suite requires a real deployed URL.

## Available Scripts

- `npm run init-theme`: Rewrite template placeholders for a new theme instance.
- `npm run build:pattern-previews`: Regenerate static pattern previews.
- `npm run build:test-previews`: Wrap previews in full HTML fixtures for Playwright.
- `npm run test:init-theme`: Validate the bootstrap script on fixture directories.
- `npm run test:e2e`: Fast mobile overflow smoke test.
- `npm run test:visual`: Mobile layout screenshots for the starter homepage and Shopify grid.
- `npm run test:a11y`: Accessibility smoke checks against local preview fixtures.
- `npm run test:business`: Analytics, navigation, and Shopify behavior checks.
- `npm run test:live:visual`: Read-only visual smoke checks against `BASE_URL` or the initialized site URL placeholder.
- `npm run build:release`: Regenerate the changelog, refresh previews, and package a ZIP under `dist/`.

## Live Visual Testing

Run the live smoke test only after you have a real site URL:

```bash
BASE_URL=https://example.com npm run test:live:visual
```

Use UI mode for debugging:

```bash
BASE_URL=https://example.com npm run test:live:ui
```

## Notes

- The Shopify pattern is optional and safe to leave unused on content-only builds.
- The starter keeps analytics generic by tracking `data-cta-type` and `data-cta-placement` attributes.
- `languages/editorial-starter.pot` is the committed placeholder catalog and will be renamed by `init-theme`.

## License

GNU GPL v2 or later.
