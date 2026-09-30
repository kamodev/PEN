<?php
/**
 * Home: hero.
 *
 * @package PEN
 */

$image = pen_mod( 'pen_hero_image' );
$style = $image ? ' style="background-image:url(' . esc_url( $image ) . ')"' : '';
?>
<section class="pen-hero"<?php echo $style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- URL escaped above. ?>>
	<div class="pen-container">
		<div class="pen-hero__content">
			<?php if ( pen_mod( 'pen_hero_eyebrow' ) ) : ?>
				<span class="pen-eyebrow"><?php echo esc_html( pen_mod( 'pen_hero_eyebrow' ) ); ?></span>
			<?php endif; ?>
			<h1><?php echo wp_kses( pen_mod( 'pen_hero_title' ), array( 'em' => array(), 'br' => array(), 'strong' => array() ) ); ?></h1>
			<p class="pen-hero__lead"><?php echo esc_html( pen_mod( 'pen_hero_text' ) ); ?></p>
			<div class="pen-hero__ctas">
				<?php if ( pen_mod( 'pen_hero_btn1_text' ) ) : ?>
					<a class="pen-btn" href="<?php echo esc_url( pen_link( pen_mod( 'pen_hero_btn1_url' ) ) ); ?>"><?php echo esc_html( pen_mod( 'pen_hero_btn1_text' ) ); ?> <?php pen_icon( 'arrow' ); ?></a>
				<?php endif; ?>
				<?php if ( pen_mod( 'pen_hero_btn2_text' ) ) : ?>
					<a class="pen-btn pen-btn--ghost" href="<?php echo esc_url( pen_link( pen_mod( 'pen_hero_btn2_url' ) ) ); ?>"><?php echo esc_html( pen_mod( 'pen_hero_btn2_text' ) ); ?></a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
