<?php
/**
 * Meta boxes for classes, instructors and testimonials.
 *
 * @package PEN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Skill level labels.
 *
 * @return array
 */
function pen_levels() {
	return array(
		''             => __( 'All levels', 'pen' ),
		'beginner'     => __( 'Beginner', 'pen' ),
		'intermediate' => __( 'Intermediate', 'pen' ),
		'advanced'     => __( 'Advanced', 'pen' ),
	);
}

/**
 * Field definitions per post type.
 *
 * @return array
 */
function pen_meta_fields() {
	/**
	 * Filter the meta box fields per post type.
	 *
	 * @param array $fields Field definitions.
	 */
	return apply_filters( 'pen_meta_fields', pen_default_meta_fields() );
}

/**
 * Built-in meta box fields.
 *
 * @return array
 */
function pen_default_meta_fields() {
	return array(
		'pen_course'      => array(
			'title'  => __( 'Class Details', 'pen' ),
			'fields' => array(
				'_pen_start_date'   => array( 'label' => __( 'Start date', 'pen' ), 'type' => 'date' ),
				'_pen_end_date'     => array( 'label' => __( 'End date (multi-day classes)', 'pen' ), 'type' => 'date' ),
				'_pen_time'         => array( 'label' => __( 'Time', 'pen' ), 'type' => 'text', 'placeholder' => '8:00 AM – 5:00 PM' ),
				'_pen_duration'     => array( 'label' => __( 'Duration', 'pen' ), 'type' => 'text', 'placeholder' => '8 hours' ),
				'_pen_location'     => array( 'label' => __( 'Location', 'pen' ), 'type' => 'text', 'placeholder' => 'Range 2 – Sanford, NC' ),
				'_pen_price'        => array( 'label' => __( 'Price', 'pen' ), 'type' => 'text', 'placeholder' => '$175' ),
				'_pen_capacity'     => array( 'label' => __( 'Capacity', 'pen' ), 'type' => 'number' ),
				'_pen_seats_left'   => array( 'label' => __( 'Seats left', 'pen' ), 'type' => 'number' ),
				'_pen_level'        => array( 'label' => __( 'Skill level', 'pen' ), 'type' => 'select', 'options' => pen_levels() ),
				'_pen_register_url' => array( 'label' => __( 'Registration URL (booking page, product or form)', 'pen' ), 'type' => 'url' ),
				'_pen_bring'        => array( 'label' => __( 'What to bring (one item per line)', 'pen' ), 'type' => 'textarea' ),
				'_pen_prereqs'      => array( 'label' => __( 'Prerequisites', 'pen' ), 'type' => 'textarea' ),
				'_pen_instructors'  => array( 'label' => __( 'Instructors', 'pen' ), 'type' => 'instructors' ),
			),
		),
		'pen_instructor'  => array(
			'title'  => __( 'Instructor Details', 'pen' ),
			'fields' => array(
				'_pen_role'        => array( 'label' => __( 'Role / title', 'pen' ), 'type' => 'text', 'placeholder' => 'Lead Medical Instructor' ),
				'_pen_credentials' => array( 'label' => __( 'Credentials', 'pen' ), 'type' => 'text', 'placeholder' => 'NREMT-P · NRA Certified · USMC Veteran' ),
			),
		),
		'pen_testimonial' => array(
			'title'  => __( 'Testimonial Details', 'pen' ),
			'fields' => array(
				'_pen_rating'    => array(
					'label'   => __( 'Rating', 'pen' ),
					'type'    => 'select',
					'options' => array( '5' => '5', '4' => '4', '3' => '3', '2' => '2', '1' => '1' ),
				),
				'_pen_course'    => array( 'label' => __( 'Class taken', 'pen' ), 'type' => 'text', 'placeholder' => 'Stop the Bleed' ),
				'_pen_hometown'  => array( 'label' => __( 'Hometown', 'pen' ), 'type' => 'text' ),
			),
		),
	);
}

/**
 * Register meta boxes.
 */
function pen_add_meta_boxes() {
	foreach ( pen_meta_fields() as $post_type => $box ) {
		add_meta_box( 'pen-details', $box['title'], 'pen_render_meta_box', $post_type, 'normal', 'high' );
	}
}
add_action( 'add_meta_boxes', 'pen_add_meta_boxes' );

/**
 * Render a meta box.
 *
 * @param WP_Post $post Current post.
 */
function pen_render_meta_box( $post ) {
	$defs = pen_meta_fields();
	if ( empty( $defs[ $post->post_type ] ) ) {
		return;
	}

	wp_nonce_field( 'pen_save_meta', 'pen_meta_nonce' );

	echo '<table class="form-table" role="presentation"><tbody>';

	foreach ( $defs[ $post->post_type ]['fields'] as $key => $field ) {
		$value = get_post_meta( $post->ID, $key, true );
		$id    = 'pen-field-' . sanitize_html_class( $key );

		echo '<tr><th scope="row"><label for="' . esc_attr( $id ) . '">' . esc_html( $field['label'] ) . '</label></th><td>';

		switch ( $field['type'] ) {
			case 'textarea':
				printf(
					'<textarea id="%1$s" name="%2$s" rows="4" class="large-text">%3$s</textarea>',
					esc_attr( $id ),
					esc_attr( $key ),
					esc_textarea( $value )
				);
				break;

			case 'select':
				printf( '<select id="%1$s" name="%2$s">', esc_attr( $id ), esc_attr( $key ) );
				foreach ( $field['options'] as $opt_value => $opt_label ) {
					printf(
						'<option value="%1$s" %2$s>%3$s</option>',
						esc_attr( $opt_value ),
						selected( (string) $value, (string) $opt_value, false ),
						esc_html( $opt_label )
					);
				}
				echo '</select>';
				break;

			case 'instructors':
				$selected    = array_map( 'absint', (array) $value );
				$instructors = get_posts(
					array(
						'post_type'      => 'pen_instructor',
						'posts_per_page' => 100,
						'orderby'        => 'title',
						'order'          => 'ASC',
					)
				);
				if ( ! $instructors ) {
					esc_html_e( 'Add instructors under Instructors → Add New.', 'pen' );
					break;
				}
				echo '<fieldset id="' . esc_attr( $id ) . '">';
				foreach ( $instructors as $instructor ) {
					printf(
						'<label style="display:block;margin-bottom:4px"><input type="checkbox" name="%1$s[]" value="%2$d" %3$s> %4$s</label>',
						esc_attr( $key ),
						(int) $instructor->ID,
						checked( in_array( $instructor->ID, $selected, true ), true, false ),
						esc_html( get_the_title( $instructor ) )
					);
				}
				echo '</fieldset>';
				break;

			default:
				printf(
					'<input type="%1$s" id="%2$s" name="%3$s" value="%4$s" placeholder="%5$s" class="%6$s" %7$s>',
					esc_attr( $field['type'] ),
					esc_attr( $id ),
					esc_attr( $key ),
					esc_attr( $value ),
					esc_attr( isset( $field['placeholder'] ) ? $field['placeholder'] : '' ),
					'number' === $field['type'] ? 'small-text' : 'regular-text',
					'number' === $field['type'] ? 'min="0" step="1"' : ''
				);
		}

		echo '</td></tr>';
	}

	echo '</tbody></table>';
}

/**
 * Save meta box values.
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post object.
 */
function pen_save_meta( $post_id, $post ) {
	if ( ! isset( $_POST['pen_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['pen_meta_nonce'] ) ), 'pen_save_meta' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$defs = pen_meta_fields();
	if ( empty( $defs[ $post->post_type ] ) ) {
		return;
	}

	foreach ( $defs[ $post->post_type ]['fields'] as $key => $field ) {
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized per type below.
		$raw = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : '';

		switch ( $field['type'] ) {
			case 'date':
				$raw   = sanitize_text_field( $raw );
				$value = preg_match( '/^\d{4}-\d{2}-\d{2}$/', $raw ) ? $raw : '';
				break;
			case 'number':
				$value = '' === $raw ? '' : absint( $raw );
				break;
			case 'url':
				$value = esc_url_raw( $raw );
				break;
			case 'textarea':
				$value = sanitize_textarea_field( $raw );
				break;
			case 'select':
				$value = array_key_exists( $raw, $field['options'] ) ? $raw : '';
				break;
			case 'instructors':
				$value = array_values( array_filter( array_map( 'absint', (array) $raw ) ) );
				break;
			default:
				$value = sanitize_text_field( $raw );
		}

		if ( '' === $value || array() === $value ) {
			delete_post_meta( $post_id, $key );
		} else {
			update_post_meta( $post_id, $key, $value );
		}
	}
}
add_action( 'save_post', 'pen_save_meta', 10, 2 );
