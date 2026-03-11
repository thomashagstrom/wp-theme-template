import { promises as fs } from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const rootDir = path.resolve(__dirname, '..');
const previewDir = path.join(rootDir, 'pattern-previews');
const outputDir = path.join(rootDir, '.test-previews');

const cssFiles = [
  path.join(rootDir, 'style.css'),
  path.join(rootDir, 'assets', 'css', 'theme.css')
];

const baseCss = `
*, *::before, *::after { box-sizing: border-box; }
html, body { margin: 0; padding: 0; }
body { min-height: 100vh; overflow-x: hidden; }
.site { min-height: 100vh; }
.site-main { display: block; }
.wp-block-group__inner-container { width: 100%; max-width: none; }
.wp-block-columns { display: flex; flex-wrap: wrap; gap: 2rem; margin: 0; }
.wp-block-column { flex: 1 1 280px; min-width: 0; }
.wp-block-post-template { list-style: none; margin: 0; padding: 0; }
.wp-block-buttons { display: flex; flex-wrap: wrap; gap: 1rem; }
.wp-block-button { display: inline-flex; max-width: 100%; }
.wp-block-button__link { display: inline-flex; align-items: center; justify-content: center; max-width: 100%; }
`;

const htmlTemplate = (title, styles, content) => `<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>${title}</title>
  <style>${styles}</style>
</head>
<body>
  <div id="page" class="site">
    <main id="primary" class="site-main">
${content}
    </main>
  </div>
</body>
</html>
`;

async function ensureReadable(filePath) {
  try {
    await fs.access(filePath);
  } catch {
    throw new Error(`Missing required file: ${filePath}`);
  }
}

async function build() {
  await ensureReadable(previewDir);

  const styleParts = [baseCss];
  for (const cssPath of cssFiles) {
    await ensureReadable(cssPath);
    styleParts.push(await fs.readFile(cssPath, 'utf8'));
  }
  const styles = styleParts.join('\n');

  const entries = await fs.readdir(previewDir, { withFileTypes: true });
  const previewFiles = entries
    .filter((entry) => entry.isFile() && entry.name.endsWith('.html'))
    .map((entry) => entry.name)
    .sort();

  if (previewFiles.length === 0) {
    throw new Error('No pattern preview HTML files were found. Run `npm run build:pattern-previews` first.');
  }

  await fs.rm(outputDir, { recursive: true, force: true });
  await fs.mkdir(outputDir, { recursive: true });

  for (const fileName of previewFiles) {
    const inputPath = path.join(previewDir, fileName);
    const outputPath = path.join(outputDir, fileName);
    const fragment = await fs.readFile(inputPath, 'utf8');
    const page = htmlTemplate(fileName, styles, fragment.trim());
    await fs.writeFile(outputPath, page, 'utf8');
  }

  process.stdout.write(`Built ${previewFiles.length} test preview pages in ${outputDir}\n`);
}

build().catch((error) => {
  process.stderr.write(`${error.message}\n`);
  process.exitCode = 1;
});
