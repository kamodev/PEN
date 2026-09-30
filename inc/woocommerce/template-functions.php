<?php
/**
 * WooCommerce template functions: header icons, cart count, home gear section.
 *
 * @package PEN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Header cart count badge.
 */
function pen_cart_count() {
	$count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
	printf( '<span class="pen-cart-count"%s>%d</span>', $count ? '' : ' hidden', (int) $count );
}

/**
 * Account and cart icons in the header.
 */
function pen_wc_header_icons() {
	?>
	<a class="pen-icon-link" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">
		<span class="screen-reader-text"><?php esc_html_e( 'My account', 'pen' ); ?></span>
		<?php pen_icon( 'user' ); ?>
	</a>
	<a class="pen-icon-link" href="<?php echo esc_url( wc_get_cart_url() ); ?>">
		<span class="screen-reader-text"><?php esc_html_e( 'Cart', 'pen' ); ?></span>
		<?php pen_icon( 'cart' ); ?>
		<?php pen_cart_count(); ?>
	</a>
	<?php
}
add_action( 'pen_header_actions', 'pen_wc_header_icons' );

/**
 * Keep the header cart count fresh after AJAX add-to-cart.
 *
 * @param array $fragments Cart fragments.
 * @return array
 */
function pen_cart_fragment( $fragments ) {
	ob_start();
	pen_cart_count();
	$fragments['.pen-cart-count'] = ob_get_clean();
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'pen_cart_fragment' );

/**
 * Add the gear section to the front page, after testimonials.
 *
 * @param string[] $sections Front page sections.
 * @return string[]
 */
function pen_wc_home_sections( $sections ) {
	$at = array_search( 'testimonials', $sections, true );
	array_splice( $sections, false === $at ? count( $sections ) : $at + 1, 0, array( 'shop' ) );
	return $sections;
}
add_filter( 'pen_front_page_sections', 'pen_wc_home_sections' );

/**
 * Render the front-page gear section.
 */
function pen_wc_home_section() {
	if ( pen_mod( 'pen_shop_enable' ) ) {
		load_template( PEN_WC_DIR . '/template-parts/home-shop.php', false );
	}
}
add_action( 'pen_home_section_shop', 'pen_wc_home_section' );
