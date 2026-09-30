<?php
/**
 * FunnelKit (Funnel Builder) alignment.
 *
 * FunnelKit builds each funnel step as its own post type and renders it with
 * its own templates ("Canvas" or "Boxed") or with a page builder such as
 * Elementor. On those steps the theme stays out of the way:
 *   - no page banner, container or sidebar around the step's content,
 *   - a minimal logo-only header and footer (or the full site header),
 *   - no announcement bar, navigation or newsletter prompts that pull buyers away.
 *
 * FunnelKit's own checkout styling wins over the theme's WooCommerce form
 * styles on funnel steps (see body.pen-funnel-step in woocommerce.css).
 *
 * @package PEN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether FunnelKit Funnel Builder is active.
 *
 * @return bool
 */
function pen_funnelkit_active() {
	return defined( 'WFFN_VERSION' ) || class_exists( 'WFFN_Core' );
}

/**
 * FunnelKit post types used for funnel steps.
 *
 * @return string[]
 */
function pen_funnel_post_types() {
	return apply_filters(
		'pen_funnel_post_types',
		array(
			'wffn_landing',   // Sales / landing page.
			'wffn_optin',     // Opt-in page.
			'wffn_oty',       // Opt-in confirmation.
			'wfacp_checkout', // Checkout.
			'wfocu_offer',    // One-click upsell / downsell (Pro).
			'wffn_ty',        // Thank-you page.
		)
	);
}

/**
 * Whether the current view is a FunnelKit funnel step.
 *
 * @return bool
 */
function pen_is_funnel_step() {
	return pen_funnelkit_active() && is_singular( pen_funnel_post_types() );
}

/**
 * Funnel steps render their own full-width content.
 *
 * @param bool $bare Whether to render bare content.
 * @return bool
 */
function pen_funnel_bare_content( $bare ) {
	return pen_is_funnel_step() ? true : $bare;
}
add_filter( 'pen_bare_content', 'pen_funnel_bare_content' );

/**
 * Minimal header and footer on funnel steps (Theme Settings → Integrations).
 *
 * @param bool $minimal Whether to use the minimal header.
 * @return bool
 */
function pen_funnel_minimal_header( $minimal ) {
	if ( pen_is_funnel_step() && 'minimal' === pen_setting( 'funnel_header' ) ) {
		return true;
	}
	return $minimal;
}
add_filter( 'pen_minimal_header', 'pen_funnel_minimal_header' );

/**
 * Body class on funnel steps.
 *
 * @param array $classes Body classes.
 * @return array
 */
function pen_funnel_body_class( $classes ) {
	if ( pen_is_funnel_step() ) {
		$classes[] = 'pen-funnel-step';
	}
	return $classes;
}
add_filter( 'body_class', 'pen_funnel_body_class' );
