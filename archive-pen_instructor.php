<?php
/**
 * Instructors archive.
 *
 * @package PEN
 */

get_header();
pen_page_hero( __( 'Our Instructors', 'pen' ), __( 'Medics, first responders, veterans and educators who have lived what they teach.', 'pen' ) );
?>
<div class="pen-section">
	<div class="pen-container">
		<?php if ( have_posts() ) : ?>
			<div class="pen-grid pen-grid--4">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/instructor-card' );
				endwhile;
				?>
			</div>
			<?php pen_pagination(); ?>
		<?php else : ?>
			<?php get_template_part( 'template-parts/content', 'none' ); ?>
		<?php endif; ?>
	</div>
</div>
<?php
get_footer();
