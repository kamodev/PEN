#!/usr/bin/env bash
# Spin up a throwaway WordPress test site for this theme.
#
# Uses PHP's built-in server and SQLite, so no MySQL or Docker is needed.
# Requirements: php (with pdo_sqlite), git, curl.
#
#   bin/test-server.sh            # install (first run) and serve on :8080
#   PORT=9000 bin/test-server.sh  # different port
#   WP_DIR=/tmp/pen-wp bin/test-server.sh
#
# Log in at http://localhost:$PORT/wp-admin with admin / admin.
set -euo pipefail

THEME_DIR="$(cd "$(dirname "$0")/.." && pwd)"
WP_DIR="${WP_DIR:-$THEME_DIR/../pen-test-site}"
PORT="${PORT:-8080}"
URL="http://localhost:$PORT"
WP_VERSION="${WP_VERSION:-6.8.3}"
SQLITE_VERSION="${SQLITE_VERSION:-v2.2.3}"

WP_CLI="$WP_DIR/wp-cli.phar"
wp() { php "$WP_CLI" --path="$WP_DIR/wp" --allow-root "$@"; }

if [ ! -f "$WP_DIR/wp/wp-config.php" ]; then
	echo "Installing WordPress $WP_VERSION into $WP_DIR ..."
	mkdir -p "$WP_DIR"
	git clone -q --depth 1 --branch "$WP_VERSION" https://github.com/WordPress/WordPress.git "$WP_DIR/wp"
	git clone -q --depth 1 --branch "$SQLITE_VERSION" https://github.com/WordPress/sqlite-database-integration.git \
		"$WP_DIR/wp/wp-content/plugins/sqlite-database-integration"
	curl -sSL -o "$WP_CLI" https://github.com/wp-cli/wp-cli/releases/download/v2.12.0/wp-cli-2.12.0.phar

	SQLITE_DIR="$WP_DIR/wp/wp-content/plugins/sqlite-database-integration"
	sed -e "s#{SQLITE_IMPLEMENTATION_FOLDER_PATH}#$SQLITE_DIR#" \
		-e "s#{SQLITE_PLUGIN}#sqlite-database-integration/load.php#" \
		"$SQLITE_DIR/db.copy" > "$WP_DIR/wp/wp-content/db.php"

	cat > "$WP_DIR/wp/wp-config.php" <<EOF
<?php
define( 'DB_NAME', 'wp' ); define( 'DB_USER', '' ); define( 'DB_PASSWORD', '' ); define( 'DB_HOST', '' );
define( 'DB_CHARSET', 'utf8' ); define( 'DB_COLLATE', '' );
define( 'DB_DIR', __DIR__ . '/wp-content/database/' ); define( 'DB_FILE', '.ht.sqlite' );
define( 'AUTH_KEY', 'pen-test' ); define( 'SECURE_AUTH_KEY', 'pen-test' ); define( 'LOGGED_IN_KEY', 'pen-test' ); define( 'NONCE_KEY', 'pen-test' );
define( 'AUTH_SALT', 'pen-test' ); define( 'SECURE_AUTH_SALT', 'pen-test' ); define( 'LOGGED_IN_SALT', 'pen-test' ); define( 'NONCE_SALT', 'pen-test' );
\$table_prefix = 'wp_';
define( 'WP_DEBUG', true ); define( 'WP_DEBUG_LOG', true ); define( 'WP_DEBUG_DISPLAY', true );
define( 'WP_HOME', '$URL' ); define( 'WP_SITEURL', '$URL' );
if ( ! defined( 'ABSPATH' ) ) define( 'ABSPATH', __DIR__ . '/' );
require_once ABSPATH . 'wp-settings.php';
EOF

	wp core install --url="$URL" --title="Preparedness Education Network" \
		--admin_user=admin --admin_password=admin --admin_email=admin@example.com --skip-email
	ln -sfn "$THEME_DIR" "$WP_DIR/wp/wp-content/themes/preparedness-network"
	wp theme activate preparedness-network
	wp rewrite structure '/%postname%/'
	wp post delete 1 --force >/dev/null 2>&1 || true
	wp eval-file "$THEME_DIR/bin/seed-demo.php"
fi

echo "Serving $URL  (admin / admin)  — Ctrl+C to stop"
cd "$WP_DIR/wp"
exec php -S "localhost:$PORT" -t "$WP_DIR/wp"
