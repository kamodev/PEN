#!/usr/bin/env bash
# Build an installable theme zip for Appearance → Themes → Add New → Upload.
#
#   bin/build-release.sh                # dist/preparedness-network-<version>.zip
#   bin/build-release.sh --allow-dirty  # build even with uncommitted changes
#
# The zip is made from the committed files (git archive), so it matches the
# repository exactly. Files marked export-ignore in .gitattributes (bin/,
# README.md, Git files) are left out. Before zipping, the script checks that
# the versions in style.css and functions.php match, that every PHP file
# lints, and that the theme has no WooCommerce template overrides. It also
# writes a SHA-256 checksum next to the zip.
set -euo pipefail

SLUG="preparedness-network"
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
DIST="$ROOT/dist"
ALLOW_DIRTY=0
[ "${1:-}" = "--allow-dirty" ] && ALLOW_DIRTY=1

cd "$ROOT"
fail() { echo "Error: $*" >&2; exit 1; }

# Versions must agree.
VERSION="$(sed -n 's/^Version:[[:space:]]*//p' style.css | tr -d '\r' | head -1)"
PHP_VERSION="$(sed -n "s/.*define( 'PEN_VERSION', '\([^']*\)' );.*/\1/p" functions.php | head -1)"
[ -n "$VERSION" ] || fail "No Version: line in style.css."
[ "$VERSION" = "$PHP_VERSION" ] || fail "style.css says $VERSION but PEN_VERSION in functions.php says $PHP_VERSION."

# Build only what is committed.
if [ "$ALLOW_DIRTY" = "0" ] && [ -n "$(git status --porcelain)" ]; then
	fail "Uncommitted changes would be left out of the zip. Commit them, or run with --allow-dirty."
fi

# Update-safe WooCommerce: no template overrides.
[ ! -d woocommerce ] || fail "A /woocommerce/ template override folder exists; the theme is meant to use hooks only."

# Lint every PHP file.
echo "Linting PHP ..."
while IFS= read -r file; do
	php -l "$file" >/dev/null || fail "PHP syntax error in $file."
done < <(git ls-files '*.php')

# Package.
mkdir -p "$DIST"
ZIP="$DIST/$SLUG-$VERSION.zip"
rm -f "$ZIP" "$ZIP.sha256"
git archive --format=zip --prefix="$SLUG/" -o "$ZIP" HEAD

# Sanity-check the package.
unzip -l "$ZIP" | grep -q " $SLUG/style.css$" || fail "style.css is missing from the zip."
unzip -l "$ZIP" | grep -q " $SLUG/bin/" && fail "Development files (bin/) ended up in the zip."

(cd "$DIST" && sha256sum "$(basename "$ZIP")" > "$(basename "$ZIP").sha256")

COUNT="$(unzip -Z1 "$ZIP" | grep -vc '/$')"
SIZE="$(du -h "$ZIP" | cut -f1)"
echo "Built $ZIP ($COUNT files, $SIZE)"
echo "Checksum: $(cut -d' ' -f1 "$ZIP.sha256")"
[ "$ALLOW_DIRTY" = "1" ] && [ -n "$(git status --porcelain)" ] && echo "Note: uncommitted changes are not in the zip (it is built from the last commit)."
exit 0
