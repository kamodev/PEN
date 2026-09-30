<?php
/**
 * WooCommerce layout hooks.
 *
 * Only WooCommerce's public actions and filters are used here; nothing
 * replaces a WooCommerce template file.
 *
 * @package PEN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * Wrap shop, category and product pages in the theme container.
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

/**
 * Open the theme wrapper.
 */
function pen_wc_wrapper_start() {
	echo '<div class="pen-section pen-store-main"><div class="pen-container">';
}
add_action( 'woocommerce_before_main_content', 'pen_wc_wrapper_start', 10 );

/**
 * Close the theme wrapper.
 */
function pen_wc_wrapper_end() {
	echo '</div></div>';
}
add_action( 'woocommerce_after_main_content', 'pen_wc_wrapper_end', 10 );

/**
 * Breadcrumbs styled like the rest of the site.
 *
 * @param array $args Breadcrumb arguments.
 * @return array
 */
function pen_wc_breadcrumb_args( $args ) {
	$args['delimiter']   = '<span class="pen-wc-crumb-sep" aria-hidden="true"> / </span>';
	$args['wrap_before'] = '<nav class="woocommerce-breadcrumb pen-breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'pen' ) . '">';
	return $args;
}
add_filter( 'woocommerce_breadcrumb_defaults', 'pen_wc_breadcrumb_args' );

/**
 * Three related products in one row.
 *
 * @param array $args Related products arguments.
 * @return array
 */
function pen_wc_related_args( $args ) {
	$args['posts_per_page'] = 3;
	$args['columns']        = 3;
	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'pen_wc_related_args' );

/**
 * Upsells in one row of three.
 *
 * @return int
 */
function pen_wc_upsell_columns() {
	return 3;
}
add_filter( 'woocommerce_upsells_columns', 'pen_wc_upsell_columns' );

/**
 * No theme sidebar on cart, checkout and account pages.
 *
 * @param string|null $context Sidebar context.
 * @return string|null
 */
function pen_wc_sidebar_context( $context ) {
	if ( is_cart() || is_checkout() || is_account_page() ) {
		return null;
	}
	return $context;
}
add_filter( 'pen_sidebar_context', 'pen_wc_sidebar_context' );

/**
 * Distraction-free header on checkout (Theme Settings → Integrations).
 *
 * The order-received page keeps the full header so customers can get back
 * to the site after buying.
 *
 * @param bool $minimal Whether to use the minimal header.
 * @return bool
 */
function pen_wc_minimal_header( $minimal ) {
	if ( is_checkout() && ! is_wc_endpoint_url( 'order-received' ) && 'minimal' === pen_setting( 'wc_checkout_header' ) ) {
		return true;
	}
	return $minimal;
}
add_filter( 'pen_minimal_header', 'pen_wc_minimal_header' );

/**
 * Template overrides the theme ships (should always be empty).
 *
 * @return string[] Relative paths of overrides found.
 */
function pen_wc_template_overrides() {
	$found = array();
	foreach ( array_unique( array( get_template_directory(), get_stylesheet_directory() ) ) as $dir ) {
		$wc_dir = $dir . '/' . WC()->template_path();
		if ( is_dir( $wc_dir ) ) {
			$files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $wc_dir, FilesystemIterator::SKIP_DOTS ) );
			foreach ( $files as $file ) {
				if ( 'php' === $file->getExtension() ) {
					$found[] = str_replace( $dir . '/', '', $file->getPathname() );
				}
			}
		}
	}
	return $found;
}
