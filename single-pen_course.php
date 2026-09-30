<?php
/**
 * Single class.
 *
 * @package PEN
 */

get_header();

while ( have_posts() ) :
	the_post();
	$c      = pen_course();
	$tracks = get_the_terms( get_the_ID(), 'pen_track' );
	$bring  = array_filter( array_map( 'trim', explode( "\n", (string) $c['bring'] ) ) );
	?>
	<header class="pen-page-hero">
		<div class="pen-container">
			<?php pen_breadcrumbs(); ?>
			<?php if ( $tracks && ! is_wp_error( $tracks ) ) : ?>
				<span class="pen-eyebrow"><?php echo esc_html( $tracks[0]->name ); ?></span>
			<?php endif; ?>
			<h1><?php the_title(); ?></h1>
			<p class="entry-meta">
				<?php echo esc_html( pen_course_dates( $c ) ); ?>
				<?php echo $c['location'] ? ' &middot; ' . esc_html( $c['location'] ) : ''; ?>
			</p>
		</div>
	</header>

	<div class="pen-section">
		<div class="pen-container pen-layout">
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<?php if ( has_post_thumbnail() ) : ?>
					<figure class="pen-featured"><?php the_post_thumbnail( 'large' ); ?></figure>
				<?php endif; ?>

				<div class="entry-content">
					<?php the_content(); ?>

					<?php if ( $c['prereqs'] ) : ?>
						<div class="pen-callout">
							<h3><?php esc_html_e( 'Prerequisites', 'pen' ); ?></h3>
							<?php echo wp_kses_post( wpautop( esc_html( $c['prereqs'] ) ) ); ?>
						</div>
					<?php endif; ?>

					<?php if ( $bring ) : ?>
						<h2><?php esc_html_e( 'What to Bring', 'pen' ); ?></h2>
						<ul class="pen-checklist">
							<?php foreach ( $bring as $item ) : ?>
								<li><?php echo esc_html( $item ); ?></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>

				<?php if ( $c['instructors'] ) : ?>
					<h2 style="margin-top:2.5rem"><?php esc_html_e( 'Your Instructors', 'pen' ); ?></h2>
					<div class="pen-grid pen-grid--3">
						<?php
						$pen_instructors = new WP_Query(
							array(
								'post_type'      => 'pen_instructor',
								'post__in'       => $c['instructors'],
								'orderby'        => 'post__in',
								'posts_per_page' => count( $c['instructors'] ),
								'no_found_rows'  => true,
							)
						);
						while ( $pen_instructors->have_posts() ) :
							$pen_instructors->the_post();
							get_template_part( 'template-parts/instructor-card' );
						endwhile;
						wp_reset_postdata();
						?>
					</div>
				<?php endif; ?>
			</article>

			<aside>
				<div class="pen-course-box">
					<?php if ( $c['price'] ) : ?>
						<div class="pen-course-box__price"><?php echo esc_html( $c['price'] ); ?></div>
					<?php endif; ?>
					<dl>
						<dt><?php esc_html_e( 'Date', 'pen' ); ?></dt>
						<dd><?php echo esc_html( pen_course_dates( $c ) ); ?></dd>
						<?php if ( $c['time'] ) : ?>
							<dt><?php esc_html_e( 'Time', 'pen' ); ?></dt>
							<dd><?php echo esc_html( $c['time'] ); ?></dd>
						<?php endif; ?>
						<?php if ( $c['duration'] ) : ?>
							<dt><?php esc_html_e( 'Length', 'pen' ); ?></dt>
							<dd><?php echo esc_html( $c['duration'] ); ?></dd>
						<?php endif; ?>
						<?php if ( $c['location'] ) : ?>
							<dt><?php esc_html_e( 'Where', 'pen' ); ?></dt>
							<dd><?php echo esc_html( $c['location'] ); ?></dd>
						<?php endif; ?>
						<dt><?php esc_html_e( 'Level', 'pen' ); ?></dt>
						<dd><?php pen_level_badge( $c ); ?></dd>
						<?php if ( null !== $c['seats'] ) : ?>
							<dt><?php esc_html_e( 'Seats', 'pen' ); ?></dt>
							<dd><?php pen_seats_badge( $c ); ?></dd>
						<?php endif; ?>
						<?php $names = pen_course_instructor_names( $c ); ?>
						<?php if ( $names ) : ?>
							<dt><?php esc_html_e( 'Instructor', 'pen' ); ?></dt>
							<dd><?php echo esc_html( $names ); ?></dd>
						<?php endif; ?>
					</dl>
					<?php pen_register_button( $c ); ?>
				</div>
			</aside>
		</div>
	</div>
	<?php
endwhile;

get_footer();
