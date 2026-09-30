<?php
/**
 * Elementor integration.
 *
 * - Elementor is the default editor: "Add New" for pages, posts, classes and
 *   instructors opens Elementor (Theme Settings → Integrations can switch
 *   back to the block editor; "Add New (Block Editor)" stays available).
 * - Pages built with Elementor render full width, without the theme banner,
 *   container or sidebar, so the Elementor layout controls the whole page.
 * - Elementor Pro Theme Builder can replace the header and footer.
 * - The Elementor global kit's colors and fonts follow Theme Settings.
 *
 * @package PEN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether Elementor is loaded.
 *
 * @return bool
 */
function pen_elementor_active() {
	return did_action( 'elementor/loaded' ) && class_exists( '\Elementor\Plugin' );
}

/**
 * Whether a post was built with Elementor.
 *
 * @param int|null $post_id Post ID (defaults to the queried object).
 * @return bool
 */
function pen_is_built_with_elementor( $post_id = null ) {
	if ( ! pen_elementor_active() ) {
		return false;
	}
	$post_id  = $post_id ? $post_id : get_queried_object_id();
	$document = $post_id ? \Elementor\Plugin::$instance->documents->get( $post_id ) : null;
	return $document && $document->is_built_with_elementor();
}

/**
 * Elementor layouts render bare, full-width content.
 *
 * @param bool $bare Whether to render bare content.
 * @return bool
 */
function pen_elementor_bare_content( $bare ) {
	return pen_is_built_with_elementor() ? true : $bare;
}
add_filter( 'pen_bare_content', 'pen_elementor_bare_content' );

/**
 * Register header, footer, single and archive locations for Elementor Pro.
 *
 * @param \ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $manager Locations manager.
 */
function pen_elementor_locations( $manager ) {
	$manager->register_all_core_location();
}
add_action( 'elementor/theme/register_locations', 'pen_elementor_locations' );

/**
 * Load elementor.css when Elementor is active.
 *
 * @param string[] $parts Stylesheet parts.
 * @return string[]
 */
function pen_elementor_style_part( $parts ) {
	if ( pen_elementor_active() ) {
		$parts[] = 'elementor';
	}
	return $parts;
}
add_filter( 'pen_style_parts', 'pen_elementor_style_part' );

/**
 * Post types Elementor edits.
 *
 * @return string[]
 */
function pen_elementor_post_types() {
	return (array) get_option( 'elementor_cpt_support', array( 'page', 'post' ) );
}

/**
 * One-time setup: let Elementor edit classes and instructors.
 */
function pen_elementor_setup() {
	if ( ! pen_elementor_active() || get_option( 'pen_elementor_setup' ) ) {
		return;
	}
	$types = array_values( array_unique( array_merge( pen_elementor_post_types(), array( 'page', 'post', 'pen_course', 'pen_instructor' ) ) ) );
	update_option( 'elementor_cpt_support', $types );
	update_option( 'pen_elementor_setup', 1 );
}
add_action( 'admin_init', 'pen_elementor_setup' );

/**
 * Whether Elementor is the default editor for new content.
 *
 * @return bool
 */
function pen_elementor_is_default_editor() {
	return pen_elementor_active() && 'elementor' === pen_setting( 'elementor_default_editor' );
}

/**
 * "Add New" opens Elementor.
 *
 * Adding ?pen-editor=block to post-new.php opens the block editor instead.
 */
function pen_elementor_redirect_new_post() {
	global $typenow;
	if ( ! pen_elementor_is_default_editor() || isset( $_GET['pen-editor'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}
	$type = $typenow ? $typenow : 'post';
	if ( ! in_array( $type, pen_elementor_post_types(), true ) ) {
		return;
	}
	$type_object = get_post_type_object( $type );
	if ( ! $type_object || ! current_user_can( $type_object->cap->create_posts ) ) {
		return;
	}
	$url = \Elementor\Plugin::$instance->documents->get_create_new_post_url( $type );
	if ( $url ) {
		wp_safe_redirect( $url );
		exit;
	}
}
add_action( 'load-post-new.php', 'pen_elementor_redirect_new_post' );

/**
 * Keep the block editor reachable: "Add New (Block Editor)" under each menu.
 */
function pen_elementor_block_editor_menus() {
	if ( ! pen_elementor_is_default_editor() ) {
		return;
	}
	foreach ( pen_elementor_post_types() as $type ) {
		$object = get_post_type_object( $type );
		if ( ! $object || ! $object->show_in_menu || ! use_block_editor_for_post_type( $type ) ) {
			continue;
		}
		$parent = 'post' === $type ? 'edit.php' : 'edit.php?post_type=' . $type;
		add_submenu_page(
			$parent,
			__( 'Add New (Block Editor)', 'pen' ),
			__( 'Add New (Block Editor)', 'pen' ),
			$object->cap->create_posts,
			'post-new.php?post_type=' . $type . '&pen-editor=block'
		);
	}
}
add_action( 'admin_menu', 'pen_elementor_block_editor_menus', 20 );

/**
 * Elementor kit values derived from Theme Settings.
 *
 * Elementor's system colors are used as: Primary = headings, Secondary =
 * subheadings, Text = body text, Accent = buttons and links.
 *
 * @return array Kit settings to merge.
 */
function pen_elementor_kit_values() {
	$system_colors = array(
		'primary'   => array( __( 'Primary', 'pen' ), pen_setting( 'color_dark' ) ),
		'secondary' => array( __( 'Secondary', 'pen' ), pen_setting( 'color_olive' ) ),
		'text'      => array( __( 'Text', 'pen' ), pen_setting( 'color_ink' ) ),
		'accent'    => array( __( 'Accent', 'pen' ), pen_setting( 'color_accent' ) ),
	);
	$colors        = array();
	foreach ( $system_colors as $id => $color ) {
		$colors[] = array(
			'_id'   => $id,
			'title' => $color[0],
			'color' => strtoupper( $color[1] ),
		);
	}

	// The rest of the theme palette, as custom colors with stable IDs.
	$custom = array();
	foreach ( array( 'color_sand', 'color_dark_2', 'color_bg', 'color_surface', 'color_surface_alt', 'color_ink_soft', 'color_border', 'color_accent_hover' ) as $key ) {
		$fields   = pen_color_fields();
		$custom[] = array(
			'_id'   => 'pen' . str_replace( array( 'color_', '_' ), '', $key ),
			'title' => sprintf( 'PEN %s', $fields[ $key ][0] ),
			'color' => strtoupper( pen_setting( $key ) ),
		);
	}

	$font = function ( $id, $title, $family, $weight, $transform = '', $spacing = null ) {
		$typo = array(
			'_id'                    => $id,
			'title'                  => $title,
			'typography_typography'  => 'custom',
			'typography_font_family' => $family,
			'typography_font_weight' => $weight,
		);
		if ( $transform ) {
			$typo['typography_text_transform'] = $transform;
		}
		if ( null !== $spacing ) {
			$typo['typography_letter_spacing'] = array(
				'unit' => 'px',
				'size' => $spacing,
			);
		}
		return $typo;
	};

	return array(
		'system_colors'     => $colors,
		'pen_custom_colors' => $custom,
		'system_typography' => array(
			$font( 'primary', __( 'Primary', 'pen' ), 'Oswald', '600', 'uppercase' ),
			$font( 'secondary', __( 'Secondary', 'pen' ), 'Oswald', '500', 'uppercase' ),
			$font( 'text', __( 'Text', 'pen' ), 'Inter', '400' ),
			$font( 'accent', __( 'Accent', 'pen' ), 'Oswald', '500', 'uppercase', 1 ),
		),
		'container_width'   => array(
			'unit' => 'px',
			'size' => 1240,
		),
	);
}

/**
 * Copy Theme Settings colors and fonts into the active Elementor kit.
 *
 * Runs in the admin whenever the resolved values change (including changes
 * to multisite network defaults), and only while the sync setting is on.
 * Custom colors the user added in Elementor are kept.
 *
 * @param bool $force Sync even if nothing changed.
 * @return bool Whether the kit was updated.
 */
function pen_elementor_sync_kit( $force = false ) {
	if ( ! pen_elementor_active() || 'on' !== pen_setting( 'elementor_sync' ) ) {
		return false;
	}
	$kit_id = (int) get_option( 'elementor_active_kit' );
	if ( ! $kit_id || 'elementor_library' !== get_post_type( $kit_id ) ) {
		return false;
	}

	$values = pen_elementor_kit_values();
	$hash   = md5( wp_json_encode( $values ) );
	if ( ! $force && get_option( 'pen_elementor_kit_hash' ) === $hash ) {
		return false;
	}

	$settings = get_post_meta( $kit_id, '_elementor_page_settings', true );
	$settings = is_array( $settings ) ? $settings : array();

	// Keep the kit's own globals from before the first sync, so they can be restored.
	if ( false === get_option( 'pen_elementor_kit_backup' ) ) {
		$backup = array( 'kit_id' => $kit_id );
		foreach ( pen_elementor_kit_keys() as $key ) {
			if ( array_key_exists( $key, $settings ) ) {
				$backup[ $key ] = $settings[ $key ];
			}
		}
		add_option( 'pen_elementor_kit_backup', $backup, '', false );
	}

	$settings['system_colors']     = $values['system_colors'];
	$settings['system_typography'] = $values['system_typography'];
	$settings['container_width']   = $values['container_width'];

	// Replace this theme's custom colors, keep everyone else's.
	$ours = wp_list_pluck( $values['pen_custom_colors'], '_id' );
	$kept = array_filter(
		isset( $settings['custom_colors'] ) ? (array) $settings['custom_colors'] : array(),
		function ( $color ) use ( $ours ) {
			return ! in_array( isset( $color['_id'] ) ? $color['_id'] : '', $ours, true );
		}
	);
	$settings['custom_colors'] = array_merge( array_values( $kept ), $values['pen_custom_colors'] );

	update_post_meta( $kit_id, '_elementor_page_settings', $settings );
	update_option( 'pen_elementor_kit_hash', $hash );

	if ( isset( \Elementor\Plugin::$instance->files_manager ) ) {
		\Elementor\Plugin::$instance->files_manager->clear_cache();
	}
	return true;
}
add_action( 'admin_init', 'pen_elementor_sync_kit', 30 );
add_action( 'customize_save_after', 'pen_elementor_sync_kit' );

/**
 * Kit settings the theme manages.
 *
 * @return string[]
 */
function pen_elementor_kit_keys() {
	return array( 'system_colors', 'system_typography', 'custom_colors', 'container_width' );
}

/**
 * Put back the kit globals saved before the first sync, and stop syncing.
 */
function pen_elementor_restore_kit() {
	check_admin_referer( 'pen_elementor_restore_kit' );
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( 'Sorry, you are not allowed to change these settings.', 'pen' ), 403 );
	}

	$backup = get_option( 'pen_elementor_kit_backup' );
	if ( is_array( $backup ) && ! empty( $backup['kit_id'] ) ) {
		$settings = get_post_meta( $backup['kit_id'], '_elementor_page_settings', true );
		$settings = is_array( $settings ) ? $settings : array();
		foreach ( pen_elementor_kit_keys() as $key ) {
			if ( array_key_exists( $key, $backup ) ) {
				$settings[ $key ] = $backup[ $key ];
			} else {
				unset( $settings[ $key ] ); // Elementor falls back to its own default.
			}
		}
		update_post_meta( $backup['kit_id'], '_elementor_page_settings', $settings );
		if ( pen_elementor_active() && isset( \Elementor\Plugin::$instance->files_manager ) ) {
			\Elementor\Plugin::$instance->files_manager->clear_cache();
		}
	}

	// Stop syncing, or the next admin page load would apply the theme values again.
	$stored                   = get_option( 'pen_settings', array() );
	$stored                   = is_array( $stored ) ? $stored : array();
	$stored['elementor_sync'] = 'off';
	update_option( 'pen_settings', $stored );
	delete_option( 'pen_elementor_kit_hash' );
	delete_option( 'pen_elementor_kit_backup' );

	wp_safe_redirect( admin_url( 'themes.php?page=pen-settings&tab=integrations&pen-kit-restored=1' ) );
	exit;
}
add_action( 'admin_post_pen_elementor_restore_kit', 'pen_elementor_restore_kit' );
