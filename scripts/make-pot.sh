#!/bin/sh

set -eu

ROOT_DIR=$(CDPATH= cd -- "$(dirname "$0")/.." && pwd)
STYLE_FILE="$ROOT_DIR/style.css"

if [ ! -f "$STYLE_FILE" ]; then
  echo "style.css not found in theme root." >&2
  exit 1
fi

TEXT_DOMAIN=$(sed -n 's/^Text Domain:[[:space:]]*//p' "$STYLE_FILE" | head -n 1 | tr -d '\r')

if [ -z "$TEXT_DOMAIN" ]; then
  echo "Could not determine Text Domain from style.css." >&2
  exit 1
fi

DESTINATION="languages/$TEXT_DOMAIN.pot"

if [ -f "$ROOT_DIR/wp-cli.phar" ]; then
  WP_CMD="php wp-cli.phar"
elif command -v wp >/dev/null 2>&1; then
  WP_CMD="wp"
else
  echo "WP-CLI is not installed. Run \`npm run setup:wp-cli\` or install \`wp\` globally first." >&2
  exit 1
fi

cd "$ROOT_DIR"

$WP_CMD i18n make-pot . "$DESTINATION" --slug="$TEXT_DOMAIN" --domain="$TEXT_DOMAIN" --skip-theme-json

echo "Generated $DESTINATION"
