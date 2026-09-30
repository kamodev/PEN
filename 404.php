<?php
/**
 * 404.
 *
 * @package PEN
 */

get_header();
pen_page_hero( __( 'Off the Map', 'pen' ), __( 'The page you were looking for could not be found.', 'pen' ) );
?>
<div class="pen-section">
	<div class="pen-container">
		<div class="entry-content" style="margin:0 auto;text-align:center">
			<p><?php esc_html_e( 'Try a search, or head to the class schedule.', 'pen' ); ?></p>
			<?php get_search_form(); ?>
			<p style="margin-top:2rem">
				<a class="pen-btn" href="<?php echo esc_url( get_post_type_archive_link( 'pen_course' ) ); ?>"><?php esc_html_e( 'View Classes', 'pen' ); ?></a>
				<a class="pen-btn pen-btn--dark" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back Home', 'pen' ); ?></a>
			</p>
		</div>
	</div>
</div>
<?php
get_footer();
