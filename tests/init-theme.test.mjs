import test from 'node:test';
import assert from 'node:assert/strict';
import { execFile } from 'node:child_process';
import { promises as fs } from 'node:fs';
import os from 'node:os';
import path from 'node:path';
import { promisify } from 'node:util';

const execFileAsync = promisify(execFile);
const scriptPath = path.resolve('scripts/init-theme.mjs');

async function createFixture(baseDirName = 'acme-journal') {
  const tempRoot = await fs.mkdtemp(path.join(os.tmpdir(), 'editorial-starter-'));
  const fixtureRoot = path.join(tempRoot, baseDirName);

  await fs.mkdir(path.join(fixtureRoot, 'languages'), { recursive: true });
  await fs.mkdir(path.join(fixtureRoot, 'tests', 'e2e'), { recursive: true });
  await fs.mkdir(path.join(fixtureRoot, 'patterns'), { recursive: true });
  await fs.mkdir(path.join(fixtureRoot, 'inc'), { recursive: true });

  await fs.writeFile(
    path.join(fixtureRoot, 'style.css'),
    [
      'Theme Name: Editorial Starter',
      'Theme URI: https://example.com/',
      'Author: Template Team',
      'Author URI: https://example.com/',
      'Text Domain: editorial-starter'
    ].join('\n'),
    'utf8'
  );
  await fs.writeFile(path.join(fixtureRoot, 'package.json'), '{"name":"editorial-starter"}\n', 'utf8');
  await fs.writeFile(path.join(fixtureRoot, 'package-lock.json'), '{"name":"editorial-starter"}\n', 'utf8');
  await fs.writeFile(path.join(fixtureRoot, 'composer.json'), '{"name":"editorial-starter/theme"}\n', 'utf8');
  await fs.writeFile(path.join(fixtureRoot, 'README.md'), 'Editorial Starter https://example.com hello@example.com', 'utf8');
  await fs.writeFile(path.join(fixtureRoot, 'llms.txt'), 'https://example.com', 'utf8');
  await fs.writeFile(path.join(fixtureRoot, 'patterns', 'shopify-signal.php'), 'shop.example.com', 'utf8');
  await fs.writeFile(path.join(fixtureRoot, 'inc', 'customizer.php'), 'hello@example.com editorial_starter EDITORIAL_STARTER', 'utf8');
  await fs.writeFile(path.join(fixtureRoot, 'tests', 'e2e', 'live-site-visual.spec.js'), 'https://example.com', 'utf8');
  await fs.writeFile(path.join(fixtureRoot, 'languages', 'editorial-starter.pot'), 'editorial-starter', 'utf8');

  return { fixtureRoot, tempRoot };
}

test('init-theme rewrites the expected placeholders', async () => {
  const { fixtureRoot, tempRoot } = await createFixture();

  await execFileAsync(
    process.execPath,
    [
      scriptPath,
      '--theme-name=Acme Journal',
      '--theme-slug=acme-journal',
      '--site-url=https://journal.example.test',
      '--contact-email=team@journal.example.test',
      '--shop-domain=store.journal.example.test'
    ],
    { cwd: fixtureRoot }
  );

  const styleCss = await fs.readFile(path.join(fixtureRoot, 'style.css'), 'utf8');
  const readme = await fs.readFile(path.join(fixtureRoot, 'README.md'), 'utf8');
  const customizer = await fs.readFile(path.join(fixtureRoot, 'inc', 'customizer.php'), 'utf8');
  const shopifyPattern = await fs.readFile(path.join(fixtureRoot, 'patterns', 'shopify-signal.php'), 'utf8');
  const potFile = await fs.readFile(path.join(fixtureRoot, 'languages', 'acme-journal.pot'), 'utf8');

  assert.match(styleCss, /Theme Name: Acme Journal/);
  assert.match(styleCss, /Text Domain: acme-journal/);
  assert.match(styleCss, /Author: Acme Journal/);
  assert.match(readme, /https:\/\/journal\.example\.test/);
  assert.match(readme, /team@journal\.example\.test/);
  assert.match(shopifyPattern, /store\.journal\.example\.test/);
  assert.match(customizer, /acme_journal/);
  assert.match(customizer, /ACME_JOURNAL/);
  assert.equal(potFile, 'acme-journal');

  await fs.rm(tempRoot, { recursive: true, force: true });
});

test('init-theme fails when required arguments are missing', async () => {
  const { fixtureRoot, tempRoot } = await createFixture();

  await assert.rejects(
    execFileAsync(process.execPath, [scriptPath, '--theme-slug=acme-journal', '--site-url=https://journal.example.test'], {
      cwd: fixtureRoot
    }),
    /Missing required `--theme-name` argument\./
  );

  await fs.rm(tempRoot, { recursive: true, force: true });
});

test('init-theme fails when the directory basename does not match the slug', async () => {
  const { fixtureRoot, tempRoot } = await createFixture('wrong-dir');

  await assert.rejects(
    execFileAsync(
      process.execPath,
      [scriptPath, '--theme-name=Acme Journal', '--theme-slug=acme-journal', '--site-url=https://journal.example.test'],
      { cwd: fixtureRoot }
    ),
    /Directory name mismatch/
  );

  await fs.rm(tempRoot, { recursive: true, force: true });
});
