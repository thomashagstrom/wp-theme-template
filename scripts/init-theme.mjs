#!/usr/bin/env node

import { promises as fs } from 'node:fs';
import path from 'node:path';

const TEXT_EXTENSIONS = new Set([
  '.css',
  '.html',
  '.js',
  '.json',
  '.md',
  '.mjs',
  '.php',
  '.pot',
  '.txt',
  '.xml',
  '.yml',
  '.yaml'
]);

const SKIP_DIRECTORIES = new Set([
  '.git',
  '.test-previews',
  'dist',
  'node_modules',
  'pattern-previews',
  'test-results',
  'vendor'
]);

function fail(message) {
  process.stderr.write(`${message}\n`);
  process.exit(1);
}

function parseArgs(argv) {
  const args = {};

  for (const entry of argv) {
    if (!entry.startsWith('--')) {
      continue;
    }

    const [key, ...rest] = entry.slice(2).split('=');
    args[key] = rest.join('=');
  }

  return args;
}

function normalizeSiteUrl(siteUrl) {
  let parsed;

  try {
    parsed = new URL(siteUrl);
  } catch {
    fail('`--site-url` must be a valid absolute URL.');
  }

  if (!['http:', 'https:'].includes(parsed.protocol)) {
    fail('`--site-url` must use http or https.');
  }

  parsed.hash = '';
  parsed.search = '';

  return parsed.toString().replace(/\/$/, '');
}

function slugToIdentifier(slug) {
  return slug.replace(/-/g, '_');
}

function slugToConstant(slug) {
  return slugToIdentifier(slug).toUpperCase();
}

function deriveContactEmail(hostname) {
  return `hello@${hostname.replace(/^www\./, '')}`;
}

function deriveShopDomain(hostname) {
  const bareHost = hostname.replace(/^www\./, '');
  return bareHost.startsWith('shop.') ? bareHost : `shop.${bareHost}`;
}

async function walkFiles(rootDir, currentDir = rootDir, files = []) {
  const entries = await fs.readdir(currentDir, { withFileTypes: true });

  for (const entry of entries) {
    const absolutePath = path.join(currentDir, entry.name);

    if (entry.isDirectory()) {
      if (!SKIP_DIRECTORIES.has(entry.name)) {
        await walkFiles(rootDir, absolutePath, files);
      }
      continue;
    }

    if (TEXT_EXTENSIONS.has(path.extname(entry.name))) {
      files.push(absolutePath);
    }
  }

  return files;
}

async function replaceInFile(filePath, replacements) {
  let contents = await fs.readFile(filePath, 'utf8');
  let changed = false;

  for (const [from, to] of replacements) {
    if (!contents.includes(from)) {
      continue;
    }

    contents = contents.split(from).join(to);
    changed = true;
  }

  if (changed) {
    await fs.writeFile(filePath, contents, 'utf8');
  }

  return changed;
}

async function renamePotFile(rootDir, themeSlug) {
  const currentPath = path.join(rootDir, 'languages', 'editorial-starter.pot');
  const nextPath = path.join(rootDir, 'languages', `${themeSlug}.pot`);

  if (themeSlug === 'editorial-starter') {
    return;
  }

  try {
    await fs.access(currentPath);
  } catch {
    return;
  }

  await fs.rename(currentPath, nextPath);
}

async function main() {
  const args = parseArgs(process.argv.slice(2));
  const themeName = String(args['theme-name'] || '').trim();
  const themeSlug = String(args['theme-slug'] || '').trim();
  const rawSiteUrl = String(args['site-url'] || '').trim();

  if (!themeName) {
    fail('Missing required `--theme-name` argument.');
  }

  if (!themeSlug) {
    fail('Missing required `--theme-slug` argument.');
  }

  if (!rawSiteUrl) {
    fail('Missing required `--site-url` argument.');
  }

  if (!/^[a-z0-9]+(?:-[a-z0-9]+)*$/.test(themeSlug)) {
    fail('`--theme-slug` must be lowercase kebab-case.');
  }

  const siteUrl = normalizeSiteUrl(rawSiteUrl);

  const currentDir = process.cwd();
  const expectedDirName = path.basename(currentDir);
  if (expectedDirName !== themeSlug) {
    fail(`Directory name mismatch: current directory is \`${expectedDirName}\`, expected \`${themeSlug}\`.`);
  }

  const url = new URL(siteUrl);
  const contactEmail = String(args['contact-email'] || '').trim() || deriveContactEmail(url.hostname);
  const shopDomain = String(args['shop-domain'] || '').trim() || deriveShopDomain(url.hostname);

  const replacements = [
    ['Editorial Starter', themeName],
    ['editorial-starter/theme', `${themeSlug}/theme`],
    ['editorial-starter', themeSlug],
    ['editorial_starter', slugToIdentifier(themeSlug)],
    ['EDITORIAL_STARTER', slugToConstant(themeSlug)],
    ['https://example.com', siteUrl],
    ['hello@example.com', contactEmail],
    ['shop.example.com', shopDomain],
    ['Template Team', themeName]
  ].sort((left, right) => right[0].length - left[0].length);

  await renamePotFile(currentDir, themeSlug);

  const files = await walkFiles(currentDir);
  let changedFiles = 0;

  for (const filePath of files) {
    const changed = await replaceInFile(filePath, replacements);
    if (changed) {
      changedFiles += 1;
    }
  }

  process.stdout.write(
    [
      `Initialized theme in ${currentDir}`,
      `Theme name: ${themeName}`,
      `Theme slug: ${themeSlug}`,
      `Site URL: ${siteUrl}`,
      `Contact email: ${contactEmail}`,
      `Shop domain: ${shopDomain}`,
      `Files updated: ${changedFiles}`
    ].join('\n') + '\n'
  );
}

main().catch((error) => {
  fail(error instanceof Error ? error.message : 'Theme initialization failed.');
});
