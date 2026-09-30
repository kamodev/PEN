<?php
/**
 * Single instructor with their upcoming classes.
 *
 * @package PEN
 */

get_header();

while ( have_posts() ) :
	the_post();
	$pen_id    = get_the_ID();
	$pen_role  = get_post_meta( $pen_id, '_pen_role', true );
	$pen_creds = get_post_meta( $pen_id, '_pen_credentials', true );
	pen_page_hero( get_the_title(), implode( ' — ', array_filter( array( $pen_role, $pen_creds ) ) ) );
	?>
	<div class="pen-section">
		<div class="pen-container pen-split" style="align-items:start">
			<div class="pen-split__media">
				<?php
				if ( has_post_thumbnail() ) {
					the_post_thumbnail( 'large' );
				} else {
					echo '<div class="pen-placeholder">' . pen_get_icon( 'user' ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
			</div>
			<div class="entry-content"><?php the_content(); ?></div>
		</div>
	</div>

	<?php
	// Upcoming classes taught by this instructor (IDs are stored as a serialized array).
	$pen_classes = pen_upcoming_courses( 50 );
	$pen_rows    = array();
	foreach ( $pen_classes->posts as $pen_class ) {
		$ids = array_map( 'absint', (array) get_post_meta( $pen_class->ID, '_pen_instructors', true ) );
		if ( in_array( $pen_id, $ids, true ) ) {
			$pen_rows[] = $pen_class;
		}
	}

	if ( $pen_rows ) :
		?>
		<section class="pen-section pen-section--alt">
			<div class="pen-container">
				<h2><?php esc_html_e( 'Upcoming Classes', 'pen' ); ?></h2>
				<div class="pen-schedule">
					<?php
					global $post;
					foreach ( $pen_rows as $post ) : // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
						setup_postdata( $post );
						get_template_part( 'template-parts/course-row' );
					endforeach;
					wp_reset_postdata();
					?>
				</div>
			</div>
		</section>
		<?php
	endif;
endwhile;

get_footer();
