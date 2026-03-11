#!/bin/sh

set -eu

ROOT_DIR=$(CDPATH= cd -- "$(dirname "$0")/.." && pwd)
TARGET="$ROOT_DIR/wp-cli.phar"
DOWNLOAD_URL="https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar"

if ! command -v php >/dev/null 2>&1; then
  echo "PHP is required to run WP-CLI." >&2
  exit 1
fi

TMP_FILE="$TARGET.tmp"
rm -f "$TMP_FILE"

if command -v curl >/dev/null 2>&1; then
  curl -fsSL "$DOWNLOAD_URL" -o "$TMP_FILE"
elif command -v wget >/dev/null 2>&1; then
  wget -q -O "$TMP_FILE" "$DOWNLOAD_URL"
else
  echo "Either curl or wget is required to download WP-CLI." >&2
  exit 1
fi

mv "$TMP_FILE" "$TARGET"

php "$TARGET" --info >/dev/null

echo "WP-CLI downloaded to $TARGET"
echo "Use it via: php wp-cli.phar"
