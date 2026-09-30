<?php
/**
 * Minimal footer for checkout and funnel steps: copyright and legal links only.
 *
 * @package PEN
 */

?>
<footer class="pen-footer pen-footer--minimal">
	<div class="pen-footer__bottom">
		<div class="pen-container">
			<span>&copy; <?php echo esc_html( gmdate( 'Y' ) . ' ' . get_bloginfo( 'name' ) ); ?></span>
			<?php
			wp_nav_menu(
				array(
					'theme_location'       => 'footer',
					'container'            => 'nav',
					'container_aria_label' => __( 'Legal', 'pen' ),
					'depth'                => 1,
					'fallback_cb'          => false,
				)
			);
			?>
		</div>
	</div>
</footer>
