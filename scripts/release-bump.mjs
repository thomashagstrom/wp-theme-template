#!/usr/bin/env node
/* eslint-disable no-console */

import { execSync } from 'node:child_process';
import { existsSync, readFileSync } from 'node:fs';

function run(command, inherit = true) {
  return execSync(command, { stdio: inherit ? 'inherit' : 'pipe' });
}

function get(command) {
  return execSync(command, { stdio: 'pipe' }).toString().trim();
}

const bump = process.argv[2];
if (!['patch', 'minor', 'major'].includes(bump)) {
  console.error('Usage: node scripts/release-bump.mjs <patch|minor|major>');
  process.exit(1);
}

const status = get('git status --porcelain');
if (status) {
  console.error('Working tree not clean. Commit or stash changes first.');
  process.exit(1);
}

console.log(`Bumping ${bump} version and building release artifacts...`);
run(`npm run build:release -- --bump=${bump}`);

const packageJson = JSON.parse(readFileSync('package.json', 'utf8'));
const newVersion = String(packageJson.version || '').trim();
if (!newVersion) {
  console.error('Unable to read version from package.json after release build.');
  process.exit(1);
}

const tagName = `v${newVersion}`;
const existingTag = get(`git tag --list ${tagName}`);
if (existingTag === tagName) {
  console.error(`Tag ${tagName} already exists. Aborting release.`);
  process.exit(1);
}

const filesToStage = ['style.css', 'package.json', 'package-lock.json', 'CHANGELOG.md'].filter((file) => existsSync(file));
if (filesToStage.length > 0) {
  run(`git add ${filesToStage.join(' ')}`);
}

try {
  run('git diff --cached --quiet', false);
  console.error('No staged release changes found. Aborting.');
  process.exit(1);
} catch {
  // Non-zero exit means staged changes exist.
}

const commitMessage = `chore(release): ${newVersion}`;
console.log(`Creating release commit: ${commitMessage}`);
run(`git commit -m "${commitMessage}"`);

console.log(`Creating annotated tag: ${tagName}`);
run(`git tag -a ${tagName} -m "${tagName}"`);

console.log('Pushing commit and tags to remote...');
run('git push');
run('git push --tags');

console.log(`Release published: ${tagName}`);
