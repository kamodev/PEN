<?php
/**
 * Home: newsletter / lead magnet band.
 *
 * @package PEN
 */

$image = pen_mod( 'pen_news_image' );
?>
<section class="pen-section pen-cta-band" id="newsletter"<?php echo $image ? ' style="background-image:url(' . esc_url( $image ) . ')"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="pen-container">
		<span class="pen-eyebrow"><?php esc_html_e( 'Free Download', 'pen' ); ?></span>
		<h2><?php echo esc_html( pen_mod( 'pen_news_title' ) ); ?></h2>
		<p><?php echo esc_html( pen_mod( 'pen_news_text' ) ); ?></p>
		<?php pen_newsletter_form(); ?>
	</div>
</section>
