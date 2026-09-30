<?php
/**
 * Customizer settings: announcement bar, header CTA, hero, stats, tracks,
 * mission, newsletter, social links and footer. Colors live in Theme Settings
 * (inc/settings.php) and are also editable here with live preview.
 *
 * @package PEN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default values for every theme mod.
 *
 * @return array
 */
function pen_defaults() {
	$defaults = array(
		'pen_announce_enable'    => true,
		'pen_announce_text'      => __( 'New Stop the Bleed & Family Preparedness classes added — seats are limited.', 'pen' ),
		'pen_announce_link'      => '/classes/',
		'pen_announce_link_text' => __( 'View schedule', 'pen' ),

		'pen_brand_tagline'      => __( 'Train · Prepare · Lead', 'pen' ),
		'pen_header_cta_text'    => __( 'Find a Class', 'pen' ),
		'pen_header_cta_url'     => '/classes/',

		'pen_hero_eyebrow'       => __( 'Preparedness Education Network', 'pen' ),
		'pen_hero_title'         => __( 'Be the one who is <em>ready</em>.', 'pen' ),
		'pen_hero_text'          => __( 'Hands-on training in emergency medicine, personal protection, and family preparedness — taught by experienced instructors, built for everyday people.', 'pen' ),
		'pen_hero_image'         => '',
		'pen_hero_btn1_text'     => __( 'View Class Schedule', 'pen' ),
		'pen_hero_btn1_url'      => '/classes/',
		'pen_hero_btn2_text'     => __( 'Free Preparedness Guide', 'pen' ),
		'pen_hero_btn2_url'      => '#newsletter',

		'pen_stats_enable'       => true,

		'pen_tracks_title'       => __( 'Training Tracks', 'pen' ),
		'pen_tracks_text'        => __( 'Build real capability one skill at a time. Every track starts at the fundamentals and progresses to scenario-based training.', 'pen' ),

		'pen_classes_title'      => __( 'Upcoming Classes', 'pen' ),
		'pen_classes_text'       => __( 'Small class sizes, real-world scenarios, and instructors who have done it for a living.', 'pen' ),
		'pen_classes_count'      => 5,

		'pen_mission_eyebrow'    => __( 'Our Mission', 'pen' ),
		'pen_mission_title'      => __( 'Preparedness is a skill. Skills can be taught.', 'pen' ),
		'pen_mission_text'       => __( 'We believe capable citizens make resilient families and communities. Our network brings together instructors, curriculum and resources so anyone can go from uncertain to ready.', 'pen' ),
		'pen_mission_points'     => "Instructor-led, hands-on classes\nCurriculum that builds from fundamentals\nFree guides, checklists and articles\nA community that trains together",
		'pen_mission_image'      => '',
		'pen_mission_btn_text'   => __( 'About the Network', 'pen' ),
		'pen_mission_btn_url'    => '/about/',

		'pen_instructors_enable' => true,
		'pen_testimonials_enable'=> true,
		'pen_resources_enable'   => true,
		'pen_shop_enable'        => true,

		'pen_news_title'         => __( 'Get the Free Family Preparedness Checklist', 'pen' ),
		'pen_news_text'          => __( 'Join the network for new class dates, training tips and our 72-hour kit checklist. No spam — unsubscribe anytime.', 'pen' ),
		'pen_news_action'        => '',
		'pen_news_field'         => 'email',
		'pen_news_button'        => __( 'Send My Checklist', 'pen' ),
		'pen_news_image'         => '',

		'pen_footer_about'       => __( 'The Preparedness Education Network connects everyday people with practical, instructor-led training in emergency medicine, safety and self-reliance.', 'pen' ),
		'pen_footer_disclaimer'  => __( 'Training content is for educational purposes only. Always follow applicable laws and seek professional medical care in an emergency.', 'pen' ),
		'pen_footer_copyright'   => '',
	);

	$stats = array(
		1 => array( '2,500+', __( 'Students Trained', 'pen' ) ),
		2 => array( '40+', __( 'Classes per Year', 'pen' ) ),
		3 => array( '12', __( 'Certified Instructors', 'pen' ) ),
		4 => array( '4.9★', __( 'Average Rating', 'pen' ) ),
	);
	foreach ( $stats as $i => $stat ) {
		$defaults[ "pen_stat{$i}_num" ]   = $stat[0];
		$defaults[ "pen_stat{$i}_label" ] = $stat[1];
	}

	$tracks = array(
		1 => array( 'medical', __( 'Emergency Medical', 'pen' ), __( 'Stop the Bleed, trauma first aid, CPR and austere medicine.', 'pen' ) ),
		2 => array( 'shield', __( 'Personal Protection', 'pen' ), __( 'Situational awareness, de-escalation and defensive skills.', 'pen' ) ),
		3 => array( 'home', __( 'Family Preparedness', 'pen' ), __( 'Go-bags, home plans, water, food storage and power.', 'pen' ) ),
		4 => array( 'radio', __( 'Comms & Navigation', 'pen' ), __( 'Ham radio basics, land navigation and grid-down planning.', 'pen' ) ),
	);
	foreach ( $tracks as $i => $track ) {
		$defaults[ "pen_track{$i}_icon" ]  = $track[0];
		$defaults[ "pen_track{$i}_title" ] = $track[1];
		$defaults[ "pen_track{$i}_text" ]  = $track[2];
		$defaults[ "pen_track{$i}_url" ]   = '/classes/';
		$defaults[ "pen_track{$i}_image" ] = '';
	}

	$social = array( 'facebook', 'instagram', 'youtube', 'x', 'rumble', 'podcast' );
	foreach ( $social as $network ) {
		$defaults[ "pen_social_{$network}" ] = '';
	}

	return apply_filters( 'pen_defaults', $defaults );
}

/**
 * Get a theme mod with the theme's default.
 *
 * @param string $key Setting key.
 * @return mixed
 */
function pen_mod( $key ) {
	$defaults = pen_defaults();
	return get_theme_mod( $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
}

/**
 * Sanitize a checkbox.
 *
 * @param mixed $value Value.
 * @return bool
 */
function pen_sanitize_checkbox( $value ) {
	return (bool) $value;
}

/**
 * Sanitize a link that may be relative ("/classes/", "#newsletter") or absolute.
 *
 * @param string $value Value.
 * @return string
 */
function pen_sanitize_link( $value ) {
	$value = trim( (string) $value );
	if ( '' === $value ) {
		return '';
	}
	if ( 0 === strpos( $value, '#' ) ) {
		return '#' . sanitize_html_class( substr( $value, 1 ) );
	}
	return esc_url_raw( $value, array( 'http', 'https', 'mailto', 'tel' ) ) ?: esc_url_raw( home_url( $value ) );
}

/**
 * Resolve a stored link to an absolute URL for output.
 *
 * @param string $value Stored link.
 * @return string
 */
function pen_link( $value ) {
	if ( '' === $value ) {
		return '';
	}
	if ( 0 === strpos( $value, '#' ) || preg_match( '#^(https?:|mailto:|tel:)#i', $value ) ) {
		return $value;
	}
	return home_url( $value );
}

/**
 * Sanitize the track icon choice.
 *
 * @param string $value Value.
 * @return string
 */
function pen_sanitize_icon( $value ) {
	return array_key_exists( $value, pen_track_icon_choices() ) ? $value : 'shield';
}

/**
 * Register Customizer panels, sections and controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 */
function pen_customize_register( $wp_customize ) {
	$d = pen_defaults();

	$add = function ( $id, $section, $label, $type = 'text', $extra = array() ) use ( $wp_customize, $d ) {
		$sanitize = array(
			'text'     => 'sanitize_text_field',
			'textarea' => 'sanitize_textarea_field',
			'html'     => 'wp_kses_post',
			'link'     => 'pen_sanitize_link',
			'url'      => 'esc_url_raw',
			'checkbox' => 'pen_sanitize_checkbox',
			'number'   => 'absint',
			'color'    => 'sanitize_hex_color',
			'image'    => 'esc_url_raw',
			'icon'     => 'pen_sanitize_icon',
		);

		$wp_customize->add_setting(
			$id,
			array(
				'default'           => isset( $d[ $id ] ) ? $d[ $id ] : '',
				'sanitize_callback' => $sanitize[ $type ],
			)
		);

		$args = array_merge(
			array(
				'label'   => $label,
				'section' => $section,
			),
			$extra
		);

		if ( 'color' === $type ) {
			$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $id, $args ) );
		} elseif ( 'image' === $type ) {
			$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, $id, $args ) );
		} else {
			$control_type = array(
				'html'     => 'textarea',
				'link'     => 'text',
				'icon'     => 'select',
			);
			$args['type'] = isset( $control_type[ $type ] ) ? $control_type[ $type ] : $type;
			if ( 'icon' === $type ) {
				$args['choices'] = pen_track_icon_choices();
			}
			$wp_customize->add_control( $id, $args );
		}
	};

	$wp_customize->add_panel(
		'pen_theme',
		array(
			'title'    => __( 'PEN Theme Options', 'pen' ),
			'priority' => 30,
		)
	);

	$sections = array(
		'pen_brand'    => __( 'Colors', 'pen' ),
		'pen_announce' => __( 'Announcement Bar', 'pen' ),
		'pen_header'   => __( 'Header', 'pen' ),
		'pen_hero'     => __( 'Home: Hero', 'pen' ),
		'pen_stats'    => __( 'Home: Stats Strip', 'pen' ),
		'pen_tracks'   => __( 'Home: Training Tracks', 'pen' ),
		'pen_classes'  => __( 'Home: Upcoming Classes', 'pen' ),
		'pen_mission'  => __( 'Home: Mission', 'pen' ),
		'pen_sections' => __( 'Home: Other Sections', 'pen' ),
		'pen_news'     => __( 'Newsletter', 'pen' ),
		'pen_social'   => __( 'Social Links', 'pen' ),
		'pen_footer'   => __( 'Footer', 'pen' ),
	);
	foreach ( $sections as $id => $title ) {
		$wp_customize->add_section( $id, array( 'title' => $title, 'panel' => 'pen_theme' ) );
	}

	$link_help = array( 'description' => __( 'Relative ("/classes/"), absolute, or an anchor ("#newsletter").', 'pen' ) );

	// Colors are stored in the pen_settings option shared with Appearance → Theme Settings.
	// When the network locks colors, the section has no controls, so it is hidden.
	if ( ! pen_is_locked( 'colors' ) ) {
		$wp_customize->get_section( 'pen_brand' )->description = sprintf(
			/* translators: %s: settings page URL. */
			__( 'Leave a color blank to use the default. Presets and readability checks are in <a href="%s">Appearance → Theme Settings</a>.', 'pen' ),
			esc_url( admin_url( 'themes.php?page=pen-settings&tab=colors' ) )
		);
		foreach ( pen_color_fields() as $key => $field ) {
			$id = 'pen_settings[' . $key . ']';
			$wp_customize->add_setting(
				$id,
				array(
					'type'              => 'option',
					'default'           => '',
					'sanitize_callback' => 'pen_sanitize_color_or_empty',
				)
			);
			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'pen_color_' . $key,
					array(
						'label'       => $field[0],
						'section'     => 'pen_brand',
						'settings'    => $id,
						/* translators: %s: color hex. */
						'description' => sprintf( __( 'Default: %s', 'pen' ), pen_inherited_setting( $key ) ),
					)
				)
			);
		}
	}

	$add( 'pen_announce_enable', 'pen_announce', __( 'Show announcement bar', 'pen' ), 'checkbox' );
	$add( 'pen_announce_text', 'pen_announce', __( 'Message', 'pen' ) );
	$add( 'pen_announce_link', 'pen_announce', __( 'Link', 'pen' ), 'link', $link_help );
	$add( 'pen_announce_link_text', 'pen_announce', __( 'Link text', 'pen' ) );

	$add( 'pen_brand_tagline', 'pen_header', __( 'Tagline under site name (when no logo)', 'pen' ) );
	$add( 'pen_header_cta_text', 'pen_header', __( 'Header button text', 'pen' ) );
	$add( 'pen_header_cta_url', 'pen_header', __( 'Header button link', 'pen' ), 'link', $link_help );

	$add( 'pen_hero_eyebrow', 'pen_hero', __( 'Eyebrow', 'pen' ) );
	$add( 'pen_hero_title', 'pen_hero', __( 'Headline (wrap a word in <em> to highlight it)', 'pen' ), 'html' );
	$add( 'pen_hero_text', 'pen_hero', __( 'Lead text', 'pen' ), 'textarea' );
	$add( 'pen_hero_image', 'pen_hero', __( 'Background image', 'pen' ), 'image' );
	$add( 'pen_hero_btn1_text', 'pen_hero', __( 'Primary button text', 'pen' ) );
	$add( 'pen_hero_btn1_url', 'pen_hero', __( 'Primary button link', 'pen' ), 'link', $link_help );
	$add( 'pen_hero_btn2_text', 'pen_hero', __( 'Secondary button text', 'pen' ) );
	$add( 'pen_hero_btn2_url', 'pen_hero', __( 'Secondary button link', 'pen' ), 'link', $link_help );

	$add( 'pen_stats_enable', 'pen_stats', __( 'Show stats strip', 'pen' ), 'checkbox' );
	for ( $i = 1; $i <= 4; $i++ ) {
		/* translators: %d: stat number. */
		$add( "pen_stat{$i}_num", 'pen_stats', sprintf( __( 'Stat %d value', 'pen' ), $i ) );
		/* translators: %d: stat number. */
		$add( "pen_stat{$i}_label", 'pen_stats', sprintf( __( 'Stat %d label', 'pen' ), $i ) );
	}

	$add( 'pen_tracks_title', 'pen_tracks', __( 'Section title', 'pen' ) );
	$add( 'pen_tracks_text', 'pen_tracks', __( 'Section intro', 'pen' ), 'textarea' );
	for ( $i = 1; $i <= 4; $i++ ) {
		/* translators: %d: track number. */
		$add( "pen_track{$i}_title", 'pen_tracks', sprintf( __( 'Track %d title', 'pen' ), $i ) );
		/* translators: %d: track number. */
		$add( "pen_track{$i}_text", 'pen_tracks', sprintf( __( 'Track %d text', 'pen' ), $i ), 'textarea' );
		/* translators: %d: track number. */
		$add( "pen_track{$i}_icon", 'pen_tracks', sprintf( __( 'Track %d icon', 'pen' ), $i ), 'icon' );
		/* translators: %d: track number. */
		$add( "pen_track{$i}_url", 'pen_tracks', sprintf( __( 'Track %d link', 'pen' ), $i ), 'link', $link_help );
		/* translators: %d: track number. */
		$add( "pen_track{$i}_image", 'pen_tracks', sprintf( __( 'Track %d image', 'pen' ), $i ), 'image' );
	}

	$add( 'pen_classes_title', 'pen_classes', __( 'Section title', 'pen' ) );
	$add( 'pen_classes_text', 'pen_classes', __( 'Section intro', 'pen' ), 'textarea' );
	$add( 'pen_classes_count', 'pen_classes', __( 'Number of classes to show', 'pen' ), 'number', array( 'input_attrs' => array( 'min' => 1, 'max' => 12 ) ) );

	$add( 'pen_mission_eyebrow', 'pen_mission', __( 'Eyebrow', 'pen' ) );
	$add( 'pen_mission_title', 'pen_mission', __( 'Title', 'pen' ) );
	$add( 'pen_mission_text', 'pen_mission', __( 'Text', 'pen' ), 'textarea' );
	$add( 'pen_mission_points', 'pen_mission', __( 'Checklist (one per line)', 'pen' ), 'textarea' );
	$add( 'pen_mission_image', 'pen_mission', __( 'Image', 'pen' ), 'image' );
	$add( 'pen_mission_btn_text', 'pen_mission', __( 'Button text', 'pen' ) );
	$add( 'pen_mission_btn_url', 'pen_mission', __( 'Button link', 'pen' ), 'link', $link_help );

	$add( 'pen_instructors_enable', 'pen_sections', __( 'Show instructors', 'pen' ), 'checkbox' );
	$add( 'pen_testimonials_enable', 'pen_sections', __( 'Show testimonials', 'pen' ), 'checkbox' );
	$add( 'pen_resources_enable', 'pen_sections', __( 'Show latest resources (blog posts)', 'pen' ), 'checkbox' );
	$add( 'pen_shop_enable', 'pen_sections', __( 'Show featured gear (requires WooCommerce)', 'pen' ), 'checkbox' );

	$add( 'pen_news_title', 'pen_news', __( 'Title', 'pen' ) );
	$add( 'pen_news_text', 'pen_news', __( 'Text', 'pen' ), 'textarea' );
	$add( 'pen_news_action', 'pen_news', __( 'Form action URL (Mailchimp, ConvertKit, etc.)', 'pen' ), 'url', array( 'description' => __( 'For Mailchimp, ConvertKit and similar. Not needed with MailPoet: choose a MailPoet list or form under Appearance → Theme Settings → Integrations instead (that takes priority). Leave empty for no form.', 'pen' ) ) );
	$add( 'pen_news_field', 'pen_news', __( 'Email field name', 'pen' ), 'text', array( 'description' => __( 'Mailchimp uses EMAIL; ConvertKit uses email_address.', 'pen' ) ) );
	$add( 'pen_news_button', 'pen_news', __( 'Button text', 'pen' ) );
	$add( 'pen_news_image', 'pen_news', __( 'Background image', 'pen' ), 'image' );

	foreach ( array( 'facebook', 'instagram', 'youtube', 'x', 'rumble', 'podcast' ) as $network ) {
		$add( "pen_social_{$network}", 'pen_social', ucfirst( $network ), 'url' );
	}

	$add( 'pen_footer_about', 'pen_footer', __( 'About text', 'pen' ), 'textarea' );
	$add( 'pen_footer_disclaimer', 'pen_footer', __( 'Disclaimer', 'pen' ), 'textarea' );
	$add( 'pen_footer_copyright', 'pen_footer', __( 'Copyright line (leave empty for default)', 'pen' ) );
}
add_action( 'customize_register', 'pen_customize_register' );

/**
 * Sanitize a color that may be blank (blank = use default).
 *
 * @param string $value Value.
 * @return string
 */
function pen_sanitize_color_or_empty( $value ) {
	$value = sanitize_hex_color( trim( (string) $value ) );
	return $value ? strtolower( $value ) : '';
}
