<?php
/**
 * Amelia booking integration (https://wpamelia.com/).
 *
 * - Classes: set an "Amelia event ID" on a class and its page shows Amelia's
 *   event booking form under "Reserve Your Seat"; Register buttons jump to it.
 * - Instructors: set an "Amelia employee ID" (and optionally a service ID) and
 *   the instructor's page shows a "Book a Private Session" form.
 * - Booking forms pick up the theme's colors and fonts (Theme Settings →
 *   Integrations → Amelia), and the Integrations tab lists the matching values
 *   for Amelia → Customize.
 *
 * Amelia can take payment through WooCommerce, which routes bookings through
 * the WooCommerce (and FunnelKit) checkout.
 *
 * @package PEN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether Amelia is active.
 *
 * @return bool
 */
function pen_amelia_active() {
	return defined( 'AMELIA_VERSION' ) || class_exists( '\\AmeliaBooking\\Plugin' ) || shortcode_exists( 'ameliabooking' ) || shortcode_exists( 'ameliastepbooking' );
}

/**
 * Render an Amelia shortcode, preferring the newer 2.x forms.
 *
 * @param string $kind  'booking' or 'events'.
 * @param array  $atts  Shortcode attributes.
 * @return string HTML, or '' when no Amelia shortcode is registered.
 */
function pen_amelia_shortcode( $kind, $atts = array() ) {
	$candidates = apply_filters(
		'pen_amelia_shortcodes',
		array(
			'booking' => array( 'ameliastepbooking', 'ameliabooking' ),
			'events'  => array( 'ameliaeventslistbooking', 'ameliaevents' ),
		)
	);
	foreach ( isset( $candidates[ $kind ] ) ? $candidates[ $kind ] : array() as $tag ) {
		if ( shortcode_exists( $tag ) ) {
			$pairs = '';
			foreach ( array_filter( $atts ) as $name => $value ) {
				$pairs .= sprintf( ' %s="%s"', sanitize_key( $name ), esc_attr( $value ) );
			}
			return do_shortcode( '[' . $tag . $pairs . ']' );
		}
	}
	return '';
}

/**
 * Amelia ID fields on classes and instructors.
 *
 * @param array $fields Meta box fields.
 * @return array
 */
function pen_amelia_meta_fields( $fields ) {
	if ( ! pen_amelia_active() ) {
		return $fields;
	}
	$fields['pen_course']['fields']['_pen_amelia_event']        = array(
		'label' => __( 'Amelia event ID (shows Amelia\'s booking form on this class)', 'pen' ),
		'type'  => 'number',
	);
	$fields['pen_instructor']['fields']['_pen_amelia_employee'] = array(
		'label' => __( 'Amelia employee ID (shows a private-session booking form)', 'pen' ),
		'type'  => 'number',
	);
	$fields['pen_instructor']['fields']['_pen_amelia_service']  = array(
		'label' => __( 'Amelia service ID (optional: limit the form to one service)', 'pen' ),
		'type'  => 'number',
	);
	return $fields;
}
add_filter( 'pen_meta_fields', 'pen_amelia_meta_fields' );

/**
 * Point Register at the class's Amelia booking form.
 *
 * @param array $data    Class details.
 * @param int   $post_id Class post ID.
 * @return array
 */
function pen_amelia_course_data( $data, $post_id ) {
	$event = (int) get_post_meta( $post_id, '_pen_amelia_event', true );
	$data['amelia_event'] = $event;
	if ( $event && pen_amelia_active() ) {
		$data['register'] = get_permalink( $post_id ) . '#register';
	}
	return $data;
}
add_filter( 'pen_course_data', 'pen_amelia_course_data', 10, 2 );

/**
 * "Reserve Your Seat" section on class pages.
 *
 * @param array $c Class details.
 */
function pen_amelia_course_booking( $c ) {
	if ( empty( $c['amelia_event'] ) || ! pen_amelia_active() ) {
		return;
	}
	$form = pen_amelia_shortcode( 'events', array( 'event' => $c['amelia_event'] ) );
	if ( '' === $form ) {
		return;
	}
	?>
	<section class="pen-booking" id="register" aria-labelledby="pen-booking-title">
		<h2 id="pen-booking-title"><?php esc_html_e( 'Reserve Your Seat', 'pen' ); ?></h2>
		<div class="pen-booking__form"><?php echo $form; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Amelia shortcode output. ?></div>
	</section>
	<?php
}
add_action( 'pen_course_after_content', 'pen_amelia_course_booking' );

/**
 * "Book a Private Session" section on instructor pages.
 *
 * @param int $instructor_id Instructor post ID.
 */
function pen_amelia_instructor_booking( $instructor_id ) {
	$employee = (int) get_post_meta( $instructor_id, '_pen_amelia_employee', true );
	if ( ! $employee || ! pen_amelia_active() ) {
		return;
	}
	$form = pen_amelia_shortcode(
		'booking',
		array(
			'employee' => $employee,
			'service'  => (int) get_post_meta( $instructor_id, '_pen_amelia_service', true ),
		)
	);
	if ( '' === $form ) {
		return;
	}
	?>
	<section class="pen-section pen-section--alt" id="book">
		<div class="pen-container pen-booking">
			<h2>
				<?php
				/* translators: %s: instructor name. */
				echo esc_html( sprintf( __( 'Book a Private Session with %s', 'pen' ), get_the_title( $instructor_id ) ) );
				?>
			</h2>
			<div class="pen-booking__form"><?php echo $form; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Amelia shortcode output. ?></div>
		</div>
	</section>
	<?php
}
add_action( 'pen_instructor_after_content', 'pen_amelia_instructor_booking' );

/**
 * Load amelia.css when Amelia is active and matching is on.
 *
 * @param string[] $parts Stylesheet parts.
 * @return string[]
 */
function pen_amelia_style_part( $parts ) {
	if ( pen_amelia_active() && 'on' === pen_setting( 'amelia_match' ) ) {
		$parts[] = 'amelia';
	}
	return $parts;
}
add_filter( 'pen_style_parts', 'pen_amelia_style_part' );

/**
 * Theme colors to enter in Amelia → Customize (shown on the Integrations tab).
 *
 * @return array label => hex
 */
function pen_amelia_color_map() {
	return array(
		__( 'Primary color', 'pen' )             => pen_setting( 'color_accent' ),
		__( 'Success color', 'pen' )             => '#3f7d3a',
		__( 'Error color', 'pen' )               => '#b3261e',
		__( 'Form background', 'pen' )           => pen_setting( 'color_surface' ),
		__( 'Heading text', 'pen' )              => pen_setting( 'color_ink' ),
		__( 'Content text', 'pen' )              => pen_setting( 'color_ink_soft' ),
		__( 'Sidebar background', 'pen' )        => pen_setting( 'color_dark' ),
		__( 'Input border', 'pen' )              => pen_setting( 'color_border' ),
		__( 'Primary button text', 'pen' )       => pen_setting( 'color_accent_ink' ),
		__( 'Font', 'pen' )                      => 'Inter',
	);
}
