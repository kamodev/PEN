<?php
/**
 * Site footer.
 *
 * @package PEN
 */

?>
</main>

<footer class="pen-footer">
	<div class="pen-footer__top">
		<div class="pen-container pen-footer__grid">
			<div class="pen-footer__about">
				<h2 class="pen-footer__heading"><?php bloginfo( 'name' ); ?></h2>
				<p><?php echo esc_html( pen_mod( 'pen_footer_about' ) ); ?></p>
				<?php pen_social_links(); ?>
			</div>

			<?php for ( $i = 1; $i <= 3; $i++ ) : ?>
				<div class="pen-footer__col">
					<?php if ( is_active_sidebar( 'footer-' . $i ) ) : ?>
						<?php dynamic_sidebar( 'footer-' . $i ); ?>
					<?php else : ?>
						<?php pen_footer_default_column( $i ); ?>
					<?php endif; ?>
				</div>
			<?php endfor; ?>
		</div>
	</div>

	<?php if ( pen_mod( 'pen_footer_disclaimer' ) ) : ?>
		<div class="pen-footer__disclaimer">
			<div class="pen-container"><?php echo esc_html( pen_mod( 'pen_footer_disclaimer' ) ); ?></div>
		</div>
	<?php endif; ?>

	<div class="pen-footer__bottom">
		<div class="pen-container">
			<span>
				<?php
				$copyright = pen_mod( 'pen_footer_copyright' );
				if ( $copyright ) {
					echo esc_html( $copyright );
				} else {
					echo '&copy; ' . esc_html( gmdate( 'Y' ) ) . ' ' . esc_html( get_bloginfo( 'name' ) ) . '. ' . esc_html__( 'All rights reserved.', 'pen' );
				}
				?>
			</span>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'footer',
					'container'      => 'nav',
					'container_aria_label' => __( 'Legal', 'pen' ),
					'depth'          => 1,
					'fallback_cb'    => false,
				)
			);
			?>
		</div>
	</div>
</footer>

<button type="button" class="pen-to-top" aria-label="<?php esc_attr_e( 'Back to top', 'pen' ); ?>"><?php pen_icon( 'up' ); ?></button>

<?php wp_footer(); ?>
</body>
</html>
