#!/usr/bin/env bash
# Spin up a throwaway WordPress test site for this theme.
#
# Uses PHP's built-in server and SQLite, so no MySQL or Docker is needed.
# Requirements: php (with pdo_sqlite), git, curl.
#
#   bin/test-server.sh                # single site on :8080
#   MULTISITE=1 bin/test-server.sh    # subdirectory network, second site at /second/
#   WITH_WOOCOMMERCE=1 bin/test-server.sh  # also install WooCommerce with demo products
#   PORT=9000 bin/test-server.sh      # different port
#   WP_DIR=/tmp/pen-wp bin/test-server.sh
#
# Log in at http://localhost:$PORT/wp-admin with admin / admin.
set -euo pipefail

THEME_DIR="$(cd "$(dirname "$0")/.." && pwd)"
MULTISITE="${MULTISITE:-0}"
if [ "$MULTISITE" = "1" ]; then
	WP_DIR="${WP_DIR:-$THEME_DIR/../pen-test-network}"
else
	WP_DIR="${WP_DIR:-$THEME_DIR/../pen-test-site}"
fi
PORT="${PORT:-8080}"
URL="http://localhost:$PORT"
WP_VERSION="${WP_VERSION:-7.1.2}"
SQLITE_VERSION="${SQLITE_VERSION:-v2.2.3}"
WITH_WOOCOMMERCE="${WITH_WOOCOMMERCE:-0}"
WC_VERSION="${WC_VERSION:-11.1.2}"

WP_CLI="$WP_DIR/wp-cli.phar"
wp() { php "$WP_CLI" --path="$WP_DIR/wp" --allow-root "$@"; }

# Set up one site: permalinks, theme, demo content.
setup_site() {
	local site_url="$1"
	wp --url="$site_url" theme activate preparedness-network
	wp --url="$site_url" rewrite structure '/%postname%/'
	wp --url="$site_url" post delete 1 --force >/dev/null 2>&1 || true
	wp --url="$site_url" eval-file "$THEME_DIR/bin/seed-demo.php"
}

if [ ! -f "$WP_DIR/wp/wp-config.php" ]; then
	echo "Installing WordPress $WP_VERSION into $WP_DIR ..."
	mkdir -p "$WP_DIR"
	git clone -q --depth 1 --branch "$WP_VERSION" https://github.com/WordPress/WordPress.git "$WP_DIR/wp"
	git clone -q --depth 1 --branch "$SQLITE_VERSION" https://github.com/WordPress/sqlite-database-integration.git \
		"$WP_DIR/wp/wp-content/plugins/sqlite-database-integration"
	curl -sSL -o "$WP_CLI" https://github.com/wp-cli/wp-cli/releases/download/v2.12.0/wp-cli-2.12.0.phar

	if [ "$WITH_WOOCOMMERCE" = "1" ]; then
		echo "Downloading WooCommerce $WC_VERSION ..."
		curl -sSL -o "$WP_DIR/woocommerce.zip" "https://github.com/woocommerce/woocommerce/releases/download/$WC_VERSION/woocommerce.zip"
		unzip -q -o "$WP_DIR/woocommerce.zip" -d "$WP_DIR/wp/wp-content/plugins/"
		rm -f "$WP_DIR/woocommerce.zip"
	fi

	SQLITE_DIR="$WP_DIR/wp/wp-content/plugins/sqlite-database-integration"
	sed -e "s#{SQLITE_IMPLEMENTATION_FOLDER_PATH}#$SQLITE_DIR#" \
		-e "s#{SQLITE_PLUGIN}#sqlite-database-integration/load.php#" \
		"$SQLITE_DIR/db.copy" > "$WP_DIR/wp/wp-content/db.php"

	# Multisite constants go in ms-config.php, written after the network is installed.
	cat > "$WP_DIR/wp/wp-config.php" <<EOF
<?php
define( 'DB_NAME', 'wp' ); define( 'DB_USER', '' ); define( 'DB_PASSWORD', '' ); define( 'DB_HOST', '' );
define( 'DB_CHARSET', 'utf8' ); define( 'DB_COLLATE', '' );
define( 'DB_DIR', __DIR__ . '/wp-content/database/' ); define( 'DB_FILE', '.ht.sqlite' );
define( 'AUTH_KEY', 'pen-test' ); define( 'SECURE_AUTH_KEY', 'pen-test' ); define( 'LOGGED_IN_KEY', 'pen-test' ); define( 'NONCE_KEY', 'pen-test' );
define( 'AUTH_SALT', 'pen-test' ); define( 'SECURE_AUTH_SALT', 'pen-test' ); define( 'LOGGED_IN_SALT', 'pen-test' ); define( 'NONCE_SALT', 'pen-test' );
\$table_prefix = 'wp_';
define( 'WP_DEBUG', true ); define( 'WP_DEBUG_LOG', true ); define( 'WP_DEBUG_DISPLAY', true );
if ( file_exists( __DIR__ . '/ms-config.php' ) ) {
	require __DIR__ . '/ms-config.php';
} else {
	define( 'WP_HOME', '$URL' ); define( 'WP_SITEURL', '$URL' );
}
if ( ! defined( 'ABSPATH' ) ) define( 'ABSPATH', __DIR__ . '/' );
require_once ABSPATH . 'wp-settings.php';
EOF

	ln -sfn "$THEME_DIR" "$WP_DIR/wp/wp-content/themes/preparedness-network"

	if [ "$MULTISITE" = "1" ]; then
		wp core multisite-install --url="$URL" --title="Preparedness Education Network" \
			--admin_user=admin --admin_password=admin --admin_email=admin@example.com --skip-email --skip-config
		cat > "$WP_DIR/wp/ms-config.php" <<EOF
<?php
define( 'WP_ALLOW_MULTISITE', true );
define( 'MULTISITE', true );
define( 'SUBDOMAIN_INSTALL', false );
define( 'DOMAIN_CURRENT_SITE', 'localhost:$PORT' );
define( 'PATH_CURRENT_SITE', '/' );
define( 'SITE_ID_CURRENT_SITE', 1 );
define( 'BLOG_ID_CURRENT_SITE', 1 );
EOF
		wp theme enable preparedness-network --network
		[ "$WITH_WOOCOMMERCE" = "1" ] && wp plugin activate woocommerce --network
		wp site create --slug=second --title="PEN Second Site" --email=admin@example.com
		setup_site "$URL/"
		setup_site "$URL/second/"
		if [ "$WITH_WOOCOMMERCE" = "1" ]; then
			wp --url="$URL/" option update woocommerce_coming_soon no
			wp --url="$URL/second/" option update woocommerce_coming_soon no
		fi
	else
		wp core install --url="$URL" --title="Preparedness Education Network" \
			--admin_user=admin --admin_password=admin --admin_email=admin@example.com --skip-email
		[ "$WITH_WOOCOMMERCE" = "1" ] && wp plugin activate woocommerce
		# New stores start in WooCommerce's "Coming soon" mode; open the test store.
		[ "$WITH_WOOCOMMERCE" = "1" ] && wp option update woocommerce_coming_soon no
		setup_site "$URL"
	fi
fi

# Router: serves real files, maps /<site>/wp-admin/... style paths of
# subdirectory sites to the core files, and sends everything else to WordPress.
cat > "$WP_DIR/router.php" <<'EOF'
<?php
$root = __DIR__ . '/wp';
$uri  = urldecode( parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) );

$path = $uri;
if ( ! file_exists( $root . $uri ) && preg_match( '#^/[\w-]+(/(wp-(admin|includes|content)|wp-[\w-]+\.php|xmlrpc\.php).*)$#', $uri, $m ) ) {
	$path = $m[1];
}
$file = $root . $path;
if ( is_dir( $file ) ) {
	$path = rtrim( $path, '/' ) . '/index.php';
	$file = $root . $path;
}
if ( is_file( $file ) ) {
	if ( $path === $uri && '.php' !== substr( $file, -4 ) ) {
		return false;
	}
	if ( '.php' !== substr( $file, -4 ) ) {
		$types = array( 'css' => 'text/css', 'js' => 'application/javascript', 'svg' => 'image/svg+xml', 'png' => 'image/png', 'jpg' => 'image/jpeg', 'gif' => 'image/gif', 'woff2' => 'font/woff2' );
		$ext   = strtolower( pathinfo( $file, PATHINFO_EXTENSION ) );
		header( 'Content-Type: ' . ( isset( $types[ $ext ] ) ? $types[ $ext ] : 'application/octet-stream' ) );
		readfile( $file );
		return true;
	}
	$_SERVER['SCRIPT_NAME']     = $path;
	$_SERVER['PHP_SELF']        = $path;
	$_SERVER['SCRIPT_FILENAME'] = $file;
	chdir( dirname( $file ) );
	require $file;
	return true;
}
$_SERVER['SCRIPT_NAME']     = '/index.php';
$_SERVER['PHP_SELF']        = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = $root . '/index.php';
chdir( $root );
require $root . '/index.php';
EOF

echo "Serving $URL  (admin / admin)  — Ctrl+C to stop"
[ "$MULTISITE" = "1" ] && echo "Second site: $URL/second/   Network admin: $URL/wp-admin/network/"
exec php -S "localhost:$PORT" -t "$WP_DIR/wp" "$WP_DIR/router.php"
