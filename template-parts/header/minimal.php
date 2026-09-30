<?php
/**
 * Minimal, distraction-free header: logo only, no navigation or announcement bar.
 * Used on checkout and FunnelKit funnel steps (see Theme Settings → Integrations).
 *
 * @package PEN
 */

?>
<header class="pen-header pen-header--minimal" id="masthead">
	<div class="pen-container pen-header__inner">
		<a class="pen-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
			<?php
			if ( has_custom_logo() ) {
				echo wp_get_attachment_image( get_theme_mod( 'custom_logo' ), 'full', false, array( 'class' => 'custom-logo', 'alt' => get_bloginfo( 'name' ) ) );
			} else {
				?>
				<span class="pen-brand__mark" aria-hidden="true">PEN</span>
				<span class="pen-brand__text"><span class="pen-brand__name"><?php bloginfo( 'name' ); ?></span></span>
			<?php } ?>
		</a>
		<?php if ( function_exists( 'is_checkout' ) && is_checkout() ) : ?>
			<span class="pen-header__secure"><?php pen_icon( 'lock' ); ?><?php esc_html_e( 'Secure checkout', 'pen' ); ?></span>
		<?php endif; ?>
	</div>
</header>
