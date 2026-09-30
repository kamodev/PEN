<?php
/**
 * Front page: featured gear grid.
 *
 * Uses WooCommerce's [products] shortcode, so product cards always come from
 * WooCommerce's own current templates.
 *
 * @package PEN
 */

?>
<section class="pen-section" id="gear">
	<div class="pen-container">
		<div class="pen-section-head">
			<span class="pen-eyebrow"><?php esc_html_e( 'Instructor-Approved Gear', 'pen' ); ?></span>
			<h2><?php esc_html_e( 'Kits, Tools & Training Aids', 'pen' ); ?></h2>
			<p><?php esc_html_e( 'The same gear we use and teach with. Every purchase supports the network.', 'pen' ); ?></p>
		</div>
		<?php echo do_shortcode( '[products limit="4" columns="4" orderby="popularity"]' ); ?>
		<div class="pen-section-foot">
			<a class="pen-btn pen-btn--dark" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Shop All Gear', 'pen' ); ?></a>
		</div>
	</div>
</section>
