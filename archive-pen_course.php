<?php
/**
 * Class schedule (also used for training-track archives).
 *
 * @package PEN
 */

get_header();

$pen_past    = isset( $_GET['when'] ) && 'past' === sanitize_key( wp_unslash( $_GET['when'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$pen_current = is_tax( 'pen_track' ) ? get_queried_object() : null;
$pen_archive = get_post_type_archive_link( 'pen_course' );

if ( $pen_current ) {
	pen_page_hero( $pen_current->name, $pen_current->description );
} else {
	pen_page_hero( __( 'Class Schedule', 'pen' ), __( 'Hands-on training in emergency medicine, protection and preparedness. Classes fill quickly — reserve your seat early.', 'pen' ) );
}

$pen_tracks = get_terms( array( 'taxonomy' => 'pen_track', 'hide_empty' => true ) );
?>
<div class="pen-section">
	<div class="pen-container">
		<nav class="pen-filters" aria-label="<?php esc_attr_e( 'Filter classes', 'pen' ); ?>">
			<a class="pen-filter<?php echo ! $pen_current && ! $pen_past ? ' is-active' : ''; ?>" href="<?php echo esc_url( $pen_archive ); ?>"><?php esc_html_e( 'All upcoming', 'pen' ); ?></a>
			<?php if ( ! is_wp_error( $pen_tracks ) ) : ?>
				<?php foreach ( $pen_tracks as $pen_track ) : ?>
					<a class="pen-filter<?php echo $pen_current && $pen_current->term_id === $pen_track->term_id ? ' is-active' : ''; ?>" href="<?php echo esc_url( get_term_link( $pen_track ) ); ?>"><?php echo esc_html( $pen_track->name ); ?></a>
				<?php endforeach; ?>
			<?php endif; ?>
			<a class="pen-filter<?php echo $pen_past ? ' is-active' : ''; ?>" href="<?php echo esc_url( add_query_arg( 'when', 'past', $pen_archive ) ); ?>"><?php esc_html_e( 'Past classes', 'pen' ); ?></a>
		</nav>

		<?php if ( have_posts() ) : ?>
			<div class="pen-schedule">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/course-row' );
				endwhile;
				?>
			</div>
			<?php pen_pagination(); ?>
		<?php else : ?>
			<div class="pen-callout">
				<h3><?php esc_html_e( 'No classes scheduled right now', 'pen' ); ?></h3>
				<p><?php esc_html_e( 'New dates are added regularly. Join the newsletter to hear first.', 'pen' ); ?></p>
				<a class="pen-btn pen-btn--sm" href="<?php echo esc_url( home_url( '/#newsletter' ) ); ?>"><?php esc_html_e( 'Get Notified', 'pen' ); ?></a>
			</div>
		<?php endif; ?>

		<div class="pen-callout" style="margin-top:3rem">
			<h3><?php esc_html_e( 'Private & group training', 'pen' ); ?></h3>
			<p><?php esc_html_e( 'Churches, businesses, schools and neighborhood groups: we bring the class to you.', 'pen' ); ?></p>
			<?php $pen_contact = get_page_by_path( 'contact' ); ?>
			<?php if ( $pen_contact ) : ?>
				<a class="pen-btn pen-btn--sm pen-btn--dark" href="<?php echo esc_url( get_permalink( $pen_contact ) ); ?>"><?php esc_html_e( 'Request Private Training', 'pen' ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</div>
<?php
get_footer();
