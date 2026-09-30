<?php
/**
 * Template helpers.
 *
 * @package PEN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether WooCommerce is active.
 *
 * @return bool
 */
function pen_has_woo() {
	return class_exists( 'WooCommerce' );
}

/**
 * Whether the current singular view should output its content without the
 * theme's page banner, container or sidebar (page-builder layouts, funnel steps).
 *
 * @return bool
 */
function pen_is_bare_content() {
	/**
	 * Filter whether to render bare content.
	 *
	 * @param bool $bare Default false.
	 */
	return is_singular() && (bool) apply_filters( 'pen_bare_content', false );
}

/**
 * Whether to use the minimal, distraction-free header and footer
 * (logo only; used for checkout and funnel steps).
 *
 * @return bool
 */
function pen_is_minimal_header() {
	/**
	 * Filter whether to use the minimal header and footer.
	 *
	 * @param bool $minimal Default false.
	 */
	return (bool) apply_filters( 'pen_minimal_header', false );
}

/**
 * Class details as an array.
 *
 * @param int|null $post_id Post ID.
 * @return array
 */
function pen_course( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$get     = function ( $key ) use ( $post_id ) {
		return get_post_meta( $post_id, $key, true );
	};

	$seats    = $get( '_pen_seats_left' );
	$register = $get( '_pen_register_url' );

	$data = array(
		'start'       => $get( '_pen_start_date' ),
		'end'         => $get( '_pen_end_date' ),
		'time'        => $get( '_pen_time' ),
		'duration'    => $get( '_pen_duration' ),
		'location'    => $get( '_pen_location' ),
		'price'       => $get( '_pen_price' ),
		'capacity'    => $get( '_pen_capacity' ),
		'seats'       => '' === $seats ? null : (int) $seats,
		'level'       => $get( '_pen_level' ),
		'register'    => $register ? $register : get_permalink( $post_id ),
		'bring'       => $get( '_pen_bring' ),
		'prereqs'     => $get( '_pen_prereqs' ),
		'instructors' => array_filter( array_map( 'absint', (array) $get( '_pen_instructors' ) ) ),
	);

	/**
	 * Filter class details (for example to point Register at a booking form).
	 *
	 * @param array $data    Class details.
	 * @param int   $post_id Class post ID.
	 */
	return apply_filters( 'pen_course_data', $data, $post_id );
}

/**
 * Human date range for a class.
 *
 * @param array $c Class data from pen_course().
 * @return string
 */
function pen_course_dates( $c ) {
	if ( ! $c['start'] ) {
		return __( 'Date TBA', 'pen' );
	}
	$start = strtotime( $c['start'] );
	$out   = date_i18n( 'D, M j, Y', $start );
	if ( $c['end'] && $c['end'] !== $c['start'] ) {
		$out = date_i18n( 'M j', $start ) . ' – ' . date_i18n( 'M j, Y', strtotime( $c['end'] ) );
	}
	return $out;
}

/**
 * Seats remaining badge.
 *
 * @param array $c Class data.
 */
function pen_seats_badge( $c ) {
	if ( null === $c['seats'] ) {
		return;
	}
	if ( $c['seats'] <= 0 ) {
		echo '<span class="pen-seats pen-seats--full">' . esc_html__( 'Sold out', 'pen' ) . '</span>';
	} elseif ( $c['seats'] <= 3 ) {
		/* translators: %d: seats left. */
		echo '<span class="pen-seats pen-seats--low">' . esc_html( sprintf( _n( 'Only %d seat left', 'Only %d seats left', $c['seats'], 'pen' ), $c['seats'] ) ) . '</span>';
	} else {
		/* translators: %d: seats left. */
		echo '<span class="pen-seats pen-seats--open">' . esc_html( sprintf( __( '%d seats open', 'pen' ), $c['seats'] ) ) . '</span>';
	}
}

/**
 * Skill level label.
 *
 * @param array $c Class data.
 */
function pen_level_badge( $c ) {
	$levels = pen_levels();
	$label  = isset( $levels[ $c['level'] ] ) ? $levels[ $c['level'] ] : $levels[''];
	echo '<span class="pen-level">' . esc_html( $label ) . '</span>';
}

/**
 * Register button for a class.
 *
 * @param array  $c     Class data.
 * @param string $extra Extra classes.
 */
function pen_register_button( $c, $extra = '' ) {
	$full = null !== $c['seats'] && $c['seats'] <= 0;
	printf(
		'<a class="pen-btn %1$s" href="%2$s"%3$s>%4$s</a>',
		esc_attr( $extra ),
		esc_url( $c['register'] ),
		$full ? ' aria-disabled="true"' : '',
		$full ? esc_html__( 'Sold Out', 'pen' ) : esc_html__( 'Register', 'pen' )
	);
}

/**
 * Posted-on meta for posts.
 */
function pen_posted_on() {
	printf(
		'<time datetime="%1$s">%2$s</time> &middot; %3$s',
		esc_attr( get_the_date( DATE_W3C ) ),
		esc_html( get_the_date() ),
		esc_html( get_the_author() )
	);
	$cats = get_the_category_list( ', ' );
	if ( $cats && 'post' === get_post_type() ) {
		echo ' &middot; ' . wp_kses_post( $cats );
	}
}

/**
 * Simple breadcrumbs.
 */
function pen_breadcrumbs() {
	// No way-out links on the front page or on distraction-free checkout and funnel steps.
	if ( is_front_page() || pen_is_minimal_header() ) {
		return;
	}
	$crumbs = array( '<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'pen' ) . '</a>' );

	if ( is_singular( 'pen_course' ) || is_tax( 'pen_track' ) ) {
		$crumbs[] = '<a href="' . esc_url( get_post_type_archive_link( 'pen_course' ) ) . '">' . esc_html__( 'Classes', 'pen' ) . '</a>';
	} elseif ( is_singular( 'pen_instructor' ) ) {
		$crumbs[] = '<a href="' . esc_url( get_post_type_archive_link( 'pen_instructor' ) ) . '">' . esc_html__( 'Instructors', 'pen' ) . '</a>';
	} elseif ( is_singular( 'post' ) ) {
		$blog = get_option( 'page_for_posts' );
		if ( $blog ) {
			$crumbs[] = '<a href="' . esc_url( get_permalink( $blog ) ) . '">' . esc_html( get_the_title( $blog ) ) . '</a>';
		}
	}

	echo '<nav class="pen-breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'pen' ) . '">' . implode( ' / ', $crumbs ) . '</nav>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
}

/**
 * Dark page banner with title.
 *
 * @param string $title    Title (plain text).
 * @param string $subtitle Optional subtitle (HTML allowed).
 */
function pen_page_hero( $title, $subtitle = '' ) {
	?>
	<header class="pen-page-hero">
		<div class="pen-container">
			<?php pen_breadcrumbs(); ?>
			<h1><?php echo esc_html( $title ); ?></h1>
			<?php if ( $subtitle ) : ?>
				<div class="pen-page-hero__sub"><?php echo wp_kses_post( wpautop( $subtitle ) ); ?></div>
			<?php endif; ?>
		</div>
	</header>
	<?php
}

/**
 * Social icon links.
 */
function pen_social_links() {
	$links = array();
	foreach ( array( 'facebook', 'instagram', 'youtube', 'x', 'rumble', 'podcast' ) as $network ) {
		$url = pen_mod( "pen_social_{$network}" );
		if ( $url ) {
			$links[ $network ] = $url;
		}
	}
	if ( ! $links ) {
		return;
	}
	echo '<div class="pen-social">';
	foreach ( $links as $network => $url ) {
		printf(
			'<a href="%1$s" target="_blank" rel="noopener"><span class="screen-reader-text">%2$s</span>%3$s</a>',
			esc_url( $url ),
			esc_html( ucfirst( $network ) ),
			pen_get_icon( $network ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);
	}
	echo '</div>';
}

/**
 * Newsletter form (or button fallback).
 */
function pen_newsletter_form() {
	/**
	 * Replace the newsletter form (the MailPoet integration uses this).
	 *
	 * @param string|null $html Form markup, or null for the theme's default form.
	 */
	$html = apply_filters( 'pen_newsletter_form_html', null );
	if ( null !== $html ) {
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped parts or plugin shortcode output.
		return;
	}

	$action = pen_mod( 'pen_news_action' );
	$field  = pen_mod( 'pen_news_field' );
	$button = pen_mod( 'pen_news_button' );

	if ( ! $action ) {
		if ( current_user_can( 'edit_theme_options' ) ) {
			echo '<p class="pen-newsletter__note">' . esc_html__( 'Admins: choose a MailPoet list under Appearance → Theme Settings → Integrations, or add your email provider\'s form URL under Customize → PEN Theme Options → Newsletter.', 'pen' ) . '</p>';
		}
		return;
	}
	?>
	<form class="pen-newsletter" action="<?php echo esc_url( $action ); ?>" method="post" target="_blank">
		<label class="screen-reader-text" for="pen-news-email"><?php esc_html_e( 'Email address', 'pen' ); ?></label>
		<input id="pen-news-email" type="email" name="<?php echo esc_attr( $field ? $field : 'email' ); ?>" placeholder="<?php esc_attr_e( 'Your email address', 'pen' ); ?>" required autocomplete="email">
		<button type="submit"><?php echo esc_html( $button ); ?></button>
	</form>
	<?php
}

/**
 * Instructor names for a class.
 *
 * @param array $c Class data.
 * @return string
 */
function pen_course_instructor_names( $c ) {
	$names = array();
	foreach ( $c['instructors'] as $id ) {
		if ( 'publish' === get_post_status( $id ) ) {
			$names[] = get_the_title( $id );
		}
	}
	return implode( ', ', $names );
}

/**
 * Primary menu fallback until a menu is assigned.
 */
function pen_menu_fallback() {
	$items = array(
		get_post_type_archive_link( 'pen_course' )     => __( 'Classes', 'pen' ),
		get_post_type_archive_link( 'pen_instructor' ) => __( 'Instructors', 'pen' ),
	);
	$blog  = get_option( 'page_for_posts' );
	$items[ $blog ? get_permalink( $blog ) : home_url( '/?post_type=post' ) ] = __( 'Resources', 'pen' );
	if ( pen_has_woo() ) {
		$items[ wc_get_page_permalink( 'shop' ) ] = __( 'Gear', 'pen' );
	}
	foreach ( array( 'about', 'contact' ) as $slug ) {
		$page = get_page_by_path( $slug );
		if ( $page ) {
			$items[ get_permalink( $page ) ] = get_the_title( $page );
		}
	}

	echo '<ul class="menu">';
	foreach ( $items as $url => $label ) {
		if ( $url ) {
			echo '<li><a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></li>';
		}
	}
	echo '</ul>';
}

/**
 * Footer column content until widgets are added.
 *
 * @param int $column Column number (1-3).
 */
function pen_footer_default_column( $column ) {
	if ( 1 === $column ) {
		echo '<h2 class="pen-footer__heading">' . esc_html__( 'Training', 'pen' ) . '</h2><ul>';
		echo '<li><a href="' . esc_url( get_post_type_archive_link( 'pen_course' ) ) . '">' . esc_html__( 'Class Schedule', 'pen' ) . '</a></li>';
		$tracks = get_terms( array( 'taxonomy' => 'pen_track', 'hide_empty' => false, 'number' => 5 ) );
		if ( ! is_wp_error( $tracks ) ) {
			foreach ( $tracks as $track ) {
				echo '<li><a href="' . esc_url( get_term_link( $track ) ) . '">' . esc_html( $track->name ) . '</a></li>';
			}
		}
		echo '<li><a href="' . esc_url( get_post_type_archive_link( 'pen_instructor' ) ) . '">' . esc_html__( 'Instructors', 'pen' ) . '</a></li>';
		echo '</ul>';
	} elseif ( 2 === $column ) {
		echo '<h2 class="pen-footer__heading">' . esc_html__( 'Resources', 'pen' ) . '</h2><ul>';
		$posts = get_posts( array( 'posts_per_page' => 5, 'ignore_sticky_posts' => true ) );
		foreach ( $posts as $item ) {
			echo '<li><a href="' . esc_url( get_permalink( $item ) ) . '">' . esc_html( get_the_title( $item ) ) . '</a></li>';
		}
		echo '</ul>';
	} else {
		echo '<h2 class="pen-footer__heading">' . esc_html__( 'Stay Ready', 'pen' ) . '</h2>';
		echo '<p>' . esc_html__( 'New class dates and field-tested tips, straight to your inbox.', 'pen' ) . '</p>';
		echo '<a class="pen-btn pen-btn--sm" href="' . esc_url( home_url( '/#newsletter' ) ) . '">' . esc_html__( 'Join the Network', 'pen' ) . '</a>';
	}
}

/**
 * Pagination wrapper.
 */
function pen_pagination() {
	the_posts_pagination(
		array(
			'class'     => 'pen-pagination',
			'mid_size'  => 1,
			'prev_text' => '&larr;<span class="screen-reader-text">' . esc_html__( 'Previous', 'pen' ) . '</span>',
			'next_text' => '<span class="screen-reader-text">' . esc_html__( 'Next', 'pen' ) . '</span>&rarr;',
		)
	);
}
