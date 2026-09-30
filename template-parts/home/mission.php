<?php
/**
 * Home: mission / about split.
 *
 * @package PEN
 */

$points = array_filter( array_map( 'trim', explode( "\n", (string) pen_mod( 'pen_mission_points' ) ) ) );
$image  = pen_mod( 'pen_mission_image' );
?>
<section class="pen-section" id="mission">
	<div class="pen-container pen-split">
		<div class="pen-split__media">
			<?php if ( $image ) : ?>
				<img src="<?php echo esc_url( $image ); ?>" alt="" loading="lazy">
			<?php else : ?>
				<div class="pen-placeholder" aria-hidden="true"><?php pen_icon( 'compass' ); ?></div>
			<?php endif; ?>
		</div>
		<div>
			<span class="pen-eyebrow"><?php echo esc_html( pen_mod( 'pen_mission_eyebrow' ) ); ?></span>
			<h2><?php echo esc_html( pen_mod( 'pen_mission_title' ) ); ?></h2>
			<p><?php echo esc_html( pen_mod( 'pen_mission_text' ) ); ?></p>
			<?php if ( $points ) : ?>
				<ul class="pen-checklist">
					<?php foreach ( $points as $point ) : ?>
						<li><?php echo esc_html( $point ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<?php if ( pen_mod( 'pen_mission_btn_text' ) ) : ?>
				<a class="pen-btn" href="<?php echo esc_url( pen_link( pen_mod( 'pen_mission_btn_url' ) ) ); ?>"><?php echo esc_html( pen_mod( 'pen_mission_btn_text' ) ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</section>
