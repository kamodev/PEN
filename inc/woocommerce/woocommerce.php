<?php
/**
 * WooCommerce and FunnelKit integration loader.
 *
 * Update-safe by design: the theme ships NO WooCommerce template overrides
 * (there is no /woocommerce/ folder). Everything is done with WooCommerce's
 * hooks, filters and CSS, so WooCommerce updates never leave the theme with
 * outdated templates (WooCommerce → Status would list them if there were any).
 *
 * Files:
 *   setup.php              Theme support, image sizes, stylesheet.
 *   hooks.php              Layout wrappers, breadcrumbs, related products, checkout header.
 *   template-functions.php Header cart and account icons, cart count fragment, home gear section.
 *   template-parts/        Theme markup rendered through WooCommerce data (not overrides).
 *   funnelkit.php          FunnelKit funnel steps (loads even without WooCommerce,
 *                          since FunnelKit also builds lead funnels).
 *
 * @package PEN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PEN_WC_DIR', __DIR__ );

require PEN_WC_DIR . '/funnelkit.php';

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

require PEN_WC_DIR . '/setup.php';
require PEN_WC_DIR . '/template-functions.php';
require PEN_WC_DIR . '/hooks.php';
