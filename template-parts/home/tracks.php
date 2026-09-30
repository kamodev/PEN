<?php
/**
 * Home: training tracks.
 *
 * @package PEN
 */

?>
<section class="pen-section" id="tracks">
	<div class="pen-container">
		<div class="pen-section-head">
			<span class="pen-eyebrow"><?php esc_html_e( 'What We Teach', 'pen' ); ?></span>
			<h2><?php echo esc_html( pen_mod( 'pen_tracks_title' ) ); ?></h2>
			<p><?php echo esc_html( pen_mod( 'pen_tracks_text' ) ); ?></p>
		</div>
		<div class="pen-grid pen-grid--4">
			<?php
			for ( $i = 1; $i <= 4; $i++ ) :
				$title = pen_mod( "pen_track{$i}_title" );
				if ( ! $title ) {
					continue;
				}
				$image = pen_mod( "pen_track{$i}_image" );
				?>
				<a class="pen-track" href="<?php echo esc_url( pen_link( pen_mod( "pen_track{$i}_url" ) ) ); ?>"<?php echo $image ? ' style="background-image:url(' . esc_url( $image ) . ')"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<span class="pen-track__icon"><?php pen_icon( pen_mod( "pen_track{$i}_icon" ) ); ?></span>
					<h3><?php echo esc_html( $title ); ?></h3>
					<p><?php echo esc_html( pen_mod( "pen_track{$i}_text" ) ); ?></p>
					<span class="pen-track__more"><?php esc_html_e( 'Explore classes', 'pen' ); ?> &rarr;</span>
				</a>
			<?php endfor; ?>
		</div>
	</div>
</section>
