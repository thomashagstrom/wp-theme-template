const path = require('node:path');
const { pathToFileURL } = require('node:url');

const ROOT_DIR = path.resolve(__dirname, '../../..');
const PREVIEW_DIR = path.join(ROOT_DIR, '.test-previews');

function getPreviewUrl(fileName) {
  return pathToFileURL(path.join(PREVIEW_DIR, fileName)).href;
}

async function gotoPreview(page, fileName) {
  await page.goto(getPreviewUrl(fileName), { waitUntil: 'domcontentloaded' });
  await page.waitForLoadState('networkidle');
}

async function getOverflowMetrics(page) {
  return page.evaluate(() => {
    const root = document.documentElement;
    return {
      clientWidth: root.clientWidth,
      scrollWidth: root.scrollWidth,
      overflow: root.scrollWidth - root.clientWidth
    };
  });
}

module.exports = {
  gotoPreview,
  getOverflowMetrics
};
