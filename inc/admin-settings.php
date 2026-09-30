<?php
/**
 * Admin screens: Appearance → Theme Settings (per site) and
 * Network Admin → Themes → PEN Theme Defaults (multisite).
 *
 * @package PEN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the admin pages.
 */
function pen_add_settings_pages() {
	$GLOBALS['pen_settings_hook'] = add_theme_page(
		__( 'Theme Settings', 'pen' ),
		__( 'Theme Settings', 'pen' ),
		'edit_theme_options',
		'pen-settings',
		'pen_render_settings_page'
	);
}
add_action( 'admin_menu', 'pen_add_settings_pages' );

/**
 * Register the network admin page.
 */
function pen_add_network_settings_page() {
	$GLOBALS['pen_network_settings_hook'] = add_submenu_page(
		'themes.php',
		__( 'PEN Theme Defaults', 'pen' ),
		__( 'PEN Theme Defaults', 'pen' ),
		'manage_network_themes',
		'pen-network-settings',
		'pen_render_network_settings_page'
	);
}
add_action( 'network_admin_menu', 'pen_add_network_settings_page' );

/**
 * Link to the settings page from the Themes screen.
 *
 * @param array $links Action links.
 * @return array
 */
function pen_settings_action_link( $links ) {
	$links[] = '<a href="' . esc_url( admin_url( 'themes.php?page=pen-settings' ) ) . '">' . esc_html__( 'Theme Settings', 'pen' ) . '</a>';
	return $links;
}
add_filter( 'theme_action_links_' . get_template(), 'pen_settings_action_link' );

/**
 * Assets for the settings screens.
 *
 * @param string $hook Current admin page hook.
 */
function pen_settings_assets( $hook ) {
	$hooks = array_filter(
		array(
			isset( $GLOBALS['pen_settings_hook'] ) ? $GLOBALS['pen_settings_hook'] : '',
			isset( $GLOBALS['pen_network_settings_hook'] ) ? $GLOBALS['pen_network_settings_hook'] : '',
		)
	);
	if ( ! in_array( $hook, $hooks, true ) ) {
		return;
	}
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_style( 'pen-admin-settings', PEN_URI . '/assets/css/admin/settings.css', array( 'wp-color-picker' ), PEN_VERSION );
	wp_enqueue_script( 'pen-admin-settings', PEN_URI . '/assets/js/admin-settings.js', array( 'jquery', 'wp-color-picker' ), PEN_VERSION, true );
	wp_localize_script(
		'pen-admin-settings',
		'penSettings',
		array(
			'low'  => __( 'Low contrast: below 4.5:1, hard to read for many people.', 'pen' ),
			'ok'   => __( 'Readable', 'pen' ),
			'lock' => __( 'Locked by network', 'pen' ),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'pen_settings_assets' );

/**
 * Where an inherited value comes from.
 *
 * @param string $key     Setting key.
 * @param bool   $network Whether rendering the network screen.
 * @return string
 */
function pen_inherit_source( $key, $network ) {
	if ( $network ) {
		return __( 'theme default', 'pen' );
	}
	$values = pen_network_settings();
	return ( isset( $values[ $key ] ) && '' !== $values[ $key ] ) ? __( 'network default', 'pen' ) : __( 'theme default', 'pen' );
}

/**
 * Value used when a field is left blank.
 *
 * @param string $key     Setting key.
 * @param bool   $network Whether rendering the network screen.
 * @return string
 */
function pen_inherit_value( $key, $network ) {
	if ( $network ) {
		$defaults = pen_setting_defaults();
		return $defaults[ $key ];
	}
	return pen_inherited_setting( $key );
}

/**
 * Layout (sidebar) fields.
 *
 * @param string $name     Input name prefix.
 * @param array  $values   Saved values.
 * @param bool   $network  Whether rendering the network screen.
 * @param bool   $disabled Whether the fields are locked.
 */
function pen_render_layout_fields( $name, $values, $network, $disabled ) {
	$fields = pen_layout_fields();
	$areas  = pen_sidebar_areas();
	?>
	<table class="form-table pen-layout-table" role="presentation">
		<tbody>
		<?php foreach ( pen_sidebar_contexts() as $context => $label ) : ?>
			<tr>
				<th scope="row"><?php echo esc_html( $label ); ?></th>
				<td>
					<fieldset <?php disabled( $disabled ); ?>>
						<legend class="screen-reader-text"><?php echo esc_html( $label ); ?></legend>
						<?php
						foreach ( array( '_sidebar' => __( 'Sidebar', 'pen' ), '_sidebar_position' => __( 'Position', 'pen' ) ) as $suffix => $field_label ) :
							$key     = $context . $suffix;
							$id      = 'pen-' . str_replace( '_', '-', $key ) . ( $network ? '-network' : '' );
							$current = isset( $values[ $key ] ) ? $values[ $key ] : '';
							$inherit = pen_inherit_value( $key, $network );
							?>
							<label class="pen-inline-field" for="<?php echo esc_attr( $id ); ?>">
								<span><?php echo esc_html( $field_label ); ?></span>
								<select id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name . '[' . $key . ']' ); ?>">
									<option value="" <?php selected( $current, '' ); ?>>
										<?php
										/* translators: 1: inherited choice, 2: where it comes from. */
										echo esc_html( sprintf( __( 'Default: %1$s (%2$s)', 'pen' ), $fields[ $key ][0][ $inherit ], pen_inherit_source( $key, $network ) ) );
										?>
									</option>
									<?php foreach ( $fields[ $key ][0] as $value => $choice ) : ?>
										<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $current, $value ); ?>><?php echo esc_html( $choice ); ?></option>
									<?php endforeach; ?>
								</select>
							</label>
						<?php endforeach; ?>
					</fieldset>
					<p class="description">
						<?php
						$area   = $areas[ $context ];
						$global = $GLOBALS['wp_registered_sidebars'];
						$area_n = isset( $global[ $area ] ) ? $global[ $area ]['name'] : $area;
						/* translators: %s: widget area name. */
						echo esc_html( sprintf( __( 'Widget area: %s.', 'pen' ), $area_n ) );
						if ( ! $network && ! is_active_sidebar( $area ) ) {
							echo ' <strong>' . esc_html__( 'It has no widgets yet, so no sidebar shows until you add some.', 'pen' ) . '</strong>';
						}
						if ( ! $network && current_user_can( 'edit_theme_options' ) ) {
							echo ' <a href="' . esc_url( admin_url( 'widgets.php' ) ) . '">' . esc_html__( 'Manage widgets', 'pen' ) . '</a>';
						}
						?>
					</p>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<p class="description"><?php esc_html_e( 'Single posts and pages can override this in the "Sidebar" box on their edit screen. The front page, class schedule, instructors and the Full Width template always use their own layouts.', 'pen' ); ?></p>
	<?php
}

/**
 * Color fields with presets, contrast checks and a live preview.
 *
 * @param string $name     Input name prefix.
 * @param array  $values   Saved values.
 * @param bool   $network  Whether rendering the network screen.
 * @param bool   $disabled Whether the fields are locked.
 */
function pen_render_color_fields( $name, $values, $network, $disabled ) {
	?>
	<div class="pen-colors" data-disabled="<?php echo $disabled ? '1' : '0'; ?>">
		<div class="pen-colors__main">
			<?php if ( ! $disabled ) : ?>
				<div class="pen-presets">
					<h2><?php esc_html_e( 'Start from a preset', 'pen' ); ?></h2>
					<p class="description"><?php esc_html_e( 'A preset fills in every color below. Nothing changes on the site until you save.', 'pen' ); ?></p>
					<div class="pen-presets__list">
						<?php foreach ( pen_color_presets() as $slug => $preset ) : ?>
							<button type="button" class="button pen-preset" data-colors="<?php echo esc_attr( wp_json_encode( $preset[1] ) ); ?>">
								<span class="pen-preset__swatches" aria-hidden="true">
									<?php foreach ( array( 'color_dark', 'color_olive', 'color_accent', 'color_bg' ) as $k ) : ?>
										<span style="background:<?php echo esc_attr( $preset[1][ $k ] ); ?>"></span>
									<?php endforeach; ?>
								</span>
								<?php echo esc_html( $preset[0] ); ?>
							</button>
						<?php endforeach; ?>
						<button type="button" class="button-link pen-clear-colors">
							<?php echo $network ? esc_html__( 'Clear all (use theme defaults)', 'pen' ) : esc_html__( 'Clear all (use defaults)', 'pen' ); ?>
						</button>
					</div>
				</div>
			<?php endif; ?>

			<table class="form-table pen-color-table" role="presentation">
				<tbody>
				<?php
				foreach ( pen_color_fields() as $key => $field ) :
					$id      = 'pen-' . str_replace( '_', '-', $key ) . ( $network ? '-network' : '' );
					$inherit = pen_inherit_value( $key, $network );
					$current = isset( $values[ $key ] ) ? $values[ $key ] : '';
					?>
					<tr>
						<th scope="row">
							<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $field[0] ); ?></label>
							<?php if ( $field[3] ) : ?>
								<p class="description"><?php echo esc_html( $field[3] ); ?></p>
							<?php endif; ?>
						</th>
						<td>
							<input type="text" class="pen-color-field" id="<?php echo esc_attr( $id ); ?>"
								name="<?php echo esc_attr( $name . '[' . $key . ']' ); ?>"
								value="<?php echo esc_attr( $current ); ?>"
								data-key="<?php echo esc_attr( $key ); ?>"
								data-var="<?php echo esc_attr( $field[2] ); ?>"
								data-inherit="<?php echo esc_attr( $inherit ); ?>"
								<?php disabled( $disabled ); ?>>
							<p class="pen-inherit">
								<span class="pen-swatch" style="background:<?php echo esc_attr( $inherit ); ?>" aria-hidden="true"></span>
								<?php
								/* translators: 1: color hex, 2: where it comes from. */
								echo esc_html( sprintf( __( 'Blank uses %1$s (%2$s)', 'pen' ), $inherit, pen_inherit_source( $key, $network ) ) );
								?>
							</p>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>

		<aside class="pen-colors__side" aria-label="<?php esc_attr_e( 'Preview', 'pen' ); ?>">
			<h2><?php esc_html_e( 'Preview', 'pen' ); ?></h2>
			<div class="pen-preview" id="pen-preview<?php echo $network ? '-network' : ''; ?>">
				<div class="pen-preview__bar"><?php esc_html_e( 'New class dates added', 'pen' ); ?></div>
				<div class="pen-preview__header">
					<span class="pen-preview__logo">PEN</span>
					<span class="pen-preview__nav"><?php esc_html_e( 'Classes', 'pen' ); ?> · <?php esc_html_e( 'Resources', 'pen' ); ?></span>
				</div>
				<div class="pen-preview__stats"><?php esc_html_e( '2,500+ students trained', 'pen' ); ?></div>
				<div class="pen-preview__body">
					<span class="pen-preview__eyebrow"><?php esc_html_e( 'Upcoming classes', 'pen' ); ?></span>
					<div class="pen-preview__card">
						<strong><?php esc_html_e( 'Stop the Bleed', 'pen' ); ?></strong>
						<span><?php esc_html_e( 'Sat, Oct 17 · Raleigh, NC', 'pen' ); ?></span>
						<span class="pen-preview__btns">
							<span class="pen-preview__btn"><?php esc_html_e( 'Register', 'pen' ); ?></span>
							<span class="pen-preview__btn pen-preview__btn--dark"><?php esc_html_e( 'Details', 'pen' ); ?></span>
						</span>
					</div>
					<div class="pen-preview__alt"><?php esc_html_e( 'Alternate section', 'pen' ); ?></div>
				</div>
				<div class="pen-preview__quote">“<?php esc_html_e( 'Best training day in years.', 'pen' ); ?>”</div>
				<div class="pen-preview__footer"><?php esc_html_e( 'Footer', 'pen' ); ?> · <span><?php esc_html_e( 'Train · Prepare · Lead', 'pen' ); ?></span></div>
			</div>
			<h3><?php esc_html_e( 'Readability', 'pen' ); ?></h3>
			<ul class="pen-contrast" data-pairs="<?php echo esc_attr( wp_json_encode( pen_contrast_pairs() ) ); ?>"></ul>
		</aside>
	</div>
	<?php
}

/**
 * Text/background pairs checked for contrast.
 *
 * @return array
 */
function pen_contrast_pairs() {
	return array(
		array( __( 'Button text on accent', 'pen' ), 'color_accent_ink', 'color_accent' ),
		array( __( 'Text on page background', 'pen' ), 'color_ink', 'color_bg' ),
		array( __( 'Secondary text on cards', 'pen' ), 'color_ink_soft', 'color_surface' ),
		array( __( 'White text on header & footer', 'pen' ), '#ffffff', 'color_dark' ),
		array( __( 'White text on secondary', 'pen' ), '#ffffff', 'color_olive' ),
		array( __( 'Accent links on header', 'pen' ), 'color_accent', 'color_dark' ),
	);
}

/**
 * Appearance → Theme Settings.
 */
function pen_render_settings_page() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	$tab    = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'layout'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$tab    = in_array( $tab, array( 'layout', 'colors', 'integrations' ), true ) ? $tab : 'layout';
	$group  = $tab;
	$locked = pen_is_locked( $group );
	$values = pen_site_settings();
	$tabs   = array(
		'layout' => __( 'Layout & Sidebars', 'pen' ),
		'colors' => __( 'Colors', 'pen' ),
		'integrations' => __( 'Integrations', 'pen' ),
	);
	?>
	<div class="wrap pen-settings">
		<h1><?php esc_html_e( 'Theme Settings', 'pen' ); ?></h1>
		<?php settings_errors(); ?>
		<?php if ( isset( $_GET['pen-kit-restored'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Elementor\'s previous colors and fonts are back, and matching is off.', 'pen' ); ?></p></div>
		<?php endif; ?>

		<nav class="nav-tab-wrapper" aria-label="<?php esc_attr_e( 'Settings sections', 'pen' ); ?>">
			<?php foreach ( $tabs as $slug => $label ) : ?>
				<a href="<?php echo esc_url( admin_url( 'themes.php?page=pen-settings&tab=' . $slug ) ); ?>" class="nav-tab<?php echo $tab === $slug ? ' nav-tab-active' : ''; ?>"<?php echo $tab === $slug ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $label ); ?></a>
			<?php endforeach; ?>
		</nav>

		<?php if ( $locked ) : ?>
			<div class="notice notice-warning inline"><p>
				<?php
				echo 'colors' === $group
					? esc_html__( 'Your network administrator has locked the colors for all sites. These are the network colors; they can only be changed in Network Admin.', 'pen' )
					: esc_html__( 'Your network administrator has locked the layout for all sites. These are the network settings; they can only be changed in Network Admin.', 'pen' );
				?>
			</p></div>
		<?php elseif ( is_multisite() ) : ?>
			<div class="notice notice-info inline"><p><?php esc_html_e( 'This site is part of a network. Anything left on Default or blank uses the network defaults, so it follows future network changes.', 'pen' ); ?></p></div>
		<?php endif; ?>

		<form method="post" action="options.php">
			<?php
			settings_fields( 'pen_settings' );
			// Marks this save as one tab's fields, to merge with the other tabs' stored values.
			echo '<input type="hidden" name="pen_settings[_tab]" value="' . esc_attr( $tab ) . '">';
			if ( 'integrations' === $tab ) {
				pen_render_integrations( $values );
			} elseif ( 'colors' === $tab ) {
				echo $locked ? '' : '<p>' . esc_html__( 'Set the colors used across the site. Leave a color blank to use the default. You can also change colors with a live preview in the Customizer.', 'pen' ) . ' <a href="' . esc_url( admin_url( 'customize.php?autofocus[section]=pen_brand' ) ) . '">' . esc_html__( 'Open the Customizer', 'pen' ) . '</a></p>';
				pen_render_color_fields( 'pen_settings', $locked ? pen_network_settings() : $values, false, $locked );
			} else {
				echo '<p>' . esc_html__( 'Choose where sidebars appear. Posts, pages and archives each have their own setting.', 'pen' ) . '</p>';
				pen_render_layout_fields( 'pen_settings', $locked ? pen_network_settings() : $values, false, $locked );
			}
			if ( ! $locked ) {
				submit_button();
			}
			?>
		</form>
	</div>
	<?php
}

/**
 * Network Admin → Themes → PEN Theme Defaults.
 */
function pen_render_network_settings_page() {
	if ( ! current_user_can( 'manage_network_themes' ) ) {
		return;
	}
	$values = pen_network_settings();
	?>
	<div class="wrap pen-settings">
		<h1><?php esc_html_e( 'PEN Theme Defaults', 'pen' ); ?></h1>
		<?php if ( isset( $_GET['updated'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Network defaults saved.', 'pen' ); ?></p></div>
		<?php endif; ?>
		<p><?php esc_html_e( 'These defaults apply to every site in the network that uses this theme, including new sites. A site admin can still override a setting on their own site unless you lock it here.', 'pen' ); ?></p>

		<form method="post" action="<?php echo esc_url( network_admin_url( 'edit.php?action=pen_network_settings' ) ); ?>">
			<?php wp_nonce_field( 'pen_network_settings' ); ?>

			<h2><?php esc_html_e( 'Locks', 'pen' ); ?></h2>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Colors', 'pen' ); ?></th>
						<td><label for="pen-lock-colors"><input type="checkbox" id="pen-lock-colors" name="pen_network[lock_colors]" value="1" <?php checked( ! empty( $values['lock_colors'] ) ); ?>> <?php esc_html_e( 'All sites use the network colors; site admins cannot change them', 'pen' ); ?></label></td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Layout', 'pen' ); ?></th>
						<td><label for="pen-lock-layout"><input type="checkbox" id="pen-lock-layout" name="pen_network[lock_layout]" value="1" <?php checked( ! empty( $values['lock_layout'] ) ); ?>> <?php esc_html_e( 'All sites use the network sidebar settings; site admins cannot change them', 'pen' ); ?></label></td>
					</tr>
				</tbody>
			</table>

			<h2><?php esc_html_e( 'Layout & Sidebars', 'pen' ); ?></h2>
			<?php pen_render_layout_fields( 'pen_network', $values, true, false ); ?>

			<h2><?php esc_html_e( 'Colors', 'pen' ); ?></h2>
			<?php pen_render_color_fields( 'pen_network', $values, true, false ); ?>

			<?php submit_button( __( 'Save Network Defaults', 'pen' ) ); ?>
		</form>
	</div>
	<?php
}

/**
 * Save network defaults.
 */
function pen_save_network_settings() {
	check_admin_referer( 'pen_network_settings' );
	if ( ! current_user_can( 'manage_network_themes' ) ) {
		wp_die( esc_html__( 'Sorry, you are not allowed to change network theme defaults.', 'pen' ), 403 );
	}
	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized by pen_sanitize_settings().
	$input = isset( $_POST['pen_network'] ) ? (array) wp_unslash( $_POST['pen_network'] ) : array();
	update_site_option( 'pen_network_settings', pen_sanitize_settings( $input, array(), true ) );
	wp_safe_redirect( add_query_arg( 'updated', '1', network_admin_url( 'themes.php?page=pen-network-settings' ) ) );
	exit;
}
add_action( 'network_admin_edit_pen_network_settings', 'pen_save_network_settings' );

/**
 * A select for one choice setting, with a "Default" option.
 *
 * @param string $name   Input name prefix.
 * @param string $key    Setting key.
 * @param array  $values Saved values.
 * @param string $label  Field label.
 */
function pen_render_choice_field( $name, $key, $values, $label ) {
	$fields  = pen_choice_fields();
	$id      = 'pen-' . str_replace( '_', '-', $key );
	$current = isset( $values[ $key ] ) ? $values[ $key ] : '';
	$inherit = pen_inherit_value( $key, false );
	?>
	<p class="pen-inline-field">
		<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label>
		<select id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name . '[' . $key . ']' ); ?>">
			<option value="" <?php selected( $current, '' ); ?>>
				<?php
				/* translators: %s: default choice. */
				echo esc_html( sprintf( __( 'Default: %s', 'pen' ), $fields[ $key ][0][ $inherit ] ) );
				?>
			</option>
			<?php foreach ( $fields[ $key ][0] as $value => $choice ) : ?>
				<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $current, $value ); ?>><?php echo esc_html( $choice ); ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<?php
}

/**
 * Plugin status with an install or activate link.
 *
 * @param string $file   Plugin file relative to the plugins folder.
 * @param bool   $active Whether the plugin is active.
 * @param string $search Search term for the plugin installer.
 */
function pen_render_plugin_status( $file, $active, $search ) {
	if ( $active ) {
		echo '<span class="pen-status pen-status--on">' . esc_html__( 'Active', 'pen' ) . '</span>';
		return;
	}
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	$installed = array_key_exists( $file, get_plugins() );
	if ( $installed ) {
		echo '<span class="pen-status">' . esc_html__( 'Installed, not active', 'pen' ) . '</span>';
		if ( current_user_can( 'activate_plugins' ) ) {
			$url = wp_nonce_url( self_admin_url( 'plugins.php?action=activate&plugin=' . rawurlencode( $file ) ), 'activate-plugin_' . $file );
			echo ' <a class="button button-small" href="' . esc_url( $url ) . '">' . esc_html__( 'Activate', 'pen' ) . '</a>';
		}
		return;
	}
	echo '<span class="pen-status">' . esc_html__( 'Not installed', 'pen' ) . '</span>';
	if ( current_user_can( 'install_plugins' ) ) {
		$url = network_admin_url( 'plugin-install.php?tab=search&type=term&s=' . rawurlencode( $search ) );
		if ( ! is_multisite() ) {
			$url = admin_url( 'plugin-install.php?tab=search&type=term&s=' . rawurlencode( $search ) );
		}
		echo ' <a class="button button-small" href="' . esc_url( $url ) . '">' . esc_html__( 'Install', 'pen' ) . '</a>';
	}
}

/**
 * Integrations tab: WooCommerce, FunnelKit, Elementor, Amelia.
 *
 * @param array $values Saved values.
 */
function pen_render_integrations( $values ) {
	$wc_active = class_exists( 'WooCommerce' );
	echo '<p>' . esc_html__( 'The theme is built to work with these plugins. Each one is optional; its settings apply once it is active.', 'pen' ) . '</p>';
	?>
	<div class="pen-integrations">
		<section class="pen-integration">
			<header><h2>WooCommerce</h2><?php pen_render_plugin_status( 'woocommerce/woocommerce.php', $wc_active, 'woocommerce' ); ?></header>
			<p><?php esc_html_e( 'Shop, product, cart, checkout and account pages use the theme layout, colors and knife-edge buttons, including the block-based cart and checkout.', 'pen' ); ?></p>
			<?php if ( $wc_active ) : ?>
				<?php $overrides = pen_wc_template_overrides(); ?>
				<p class="pen-check <?php echo $overrides ? 'is-warn' : 'is-ok'; ?>">
					<?php
					if ( $overrides ) {
						/* translators: %s: list of template files. */
						echo esc_html( sprintf( __( 'Template overrides found: %s. These can go out of date when WooCommerce updates; check WooCommerce → Status.', 'pen' ), implode( ', ', $overrides ) ) );
					} else {
						/* translators: %s: WooCommerce version. */
						echo esc_html( sprintf( __( 'Update-safe: the theme overrides no WooCommerce templates, so WooCommerce updates (now %s) never leave theme files out of date.', 'pen' ), WC()->version ) );
					}
					?>
				</p>
			<?php endif; ?>
			<?php pen_render_choice_field( 'pen_settings', 'wc_checkout_header', $values, __( 'Checkout page header', 'pen' ) ); ?>
		</section>

		<section class="pen-integration">
			<header><h2>FunnelKit</h2><?php pen_render_plugin_status( 'funnel-builder/funnel-builder.php', pen_funnelkit_active(), 'funnelkit' ); ?></header>
			<p><?php esc_html_e( 'Funnel steps (sales, opt-in, checkout, upsell and thank-you pages) show only their own content: no page banner, sidebar, navigation or announcement bar. FunnelKit\'s checkout designs keep their own form styles.', 'pen' ); ?></p>
			<?php pen_render_choice_field( 'pen_settings', 'funnel_header', $values, __( 'Funnel step header', 'pen' ) ); ?>
			<p class="description"><?php esc_html_e( 'For a completely blank step, choose FunnelKit\'s "Canvas" template on that step instead.', 'pen' ); ?></p>
		</section>

		<section class="pen-integration">
			<header><h2>Elementor</h2><?php pen_render_plugin_status( 'elementor/elementor.php', pen_elementor_active(), 'elementor' ); ?></header>
			<p><?php esc_html_e( 'Pages built with Elementor run full width under the theme header. Elementor Pro\'s Theme Builder can replace the header and footer.', 'pen' ); ?></p>
			<?php pen_render_choice_field( 'pen_settings', 'elementor_default_editor', $values, __( 'Editor for new pages, posts, classes and instructors', 'pen' ) ); ?>
			<p class="description"><?php esc_html_e( 'With Elementor as the default, "Add New" opens Elementor. "Add New (Block Editor)" stays in each menu.', 'pen' ); ?></p>
			<?php pen_render_choice_field( 'pen_settings', 'elementor_sync', $values, __( 'Elementor global colors and fonts', 'pen' ) ); ?>
			<p class="description"><?php esc_html_e( 'When matched, Elementor\'s global colors follow the Colors tab (Primary = Header & footer, Secondary = Secondary, Text = Text, Accent = Accent) and its global fonts use Oswald and Inter. Changes made to those four colors inside Elementor are replaced on the next sync; your own custom colors are kept.', 'pen' ); ?></p>
			<?php if ( get_option( 'pen_elementor_kit_backup' ) ) : ?>
				<p>
					<?php
					$restore_url = wp_nonce_url( admin_url( 'admin-post.php?action=pen_elementor_restore_kit' ), 'pen_elementor_restore_kit' );
					?>
					<a class="button" href="<?php echo esc_url( $restore_url ); ?>"><?php esc_html_e( 'Restore Elementor\'s previous colors and fonts', 'pen' ); ?></a>
				</p>
				<p class="description"><?php esc_html_e( 'Puts back the global colors, fonts and content width Elementor had before the theme first matched them, and turns matching off.', 'pen' ); ?></p>
			<?php endif; ?>
		</section>

		<section class="pen-integration">
			<header><h2>Amelia</h2><?php pen_render_plugin_status( 'ameliabooking/ameliabooking.php', pen_amelia_active(), 'amelia booking' ); ?></header>
			<p><?php esc_html_e( 'Link a class to an Amelia event (Class Details → "Amelia event ID") to show Amelia\'s booking form on the class page and point its Register buttons there. Add an "Amelia employee ID" to an instructor to offer private-session booking on their page.', 'pen' ); ?></p>
			<?php pen_render_choice_field( 'pen_settings', 'amelia_match', $values, __( 'Booking form style', 'pen' ) ); ?>
			<details class="pen-amelia-colors">
				<summary><?php esc_html_e( 'Matching values for Amelia → Customize', 'pen' ); ?></summary>
				<p class="description"><?php esc_html_e( 'Entering these in Amelia keeps its emails, customer panel and any form the theme style does not reach consistent with the site.', 'pen' ); ?></p>
				<table class="widefat striped">
					<tbody>
					<?php foreach ( pen_amelia_color_map() as $label => $value ) : ?>
						<tr>
							<th scope="row"><?php echo esc_html( $label ); ?></th>
							<td>
								<?php if ( '#' === substr( $value, 0, 1 ) ) : ?>
									<span class="pen-swatch" style="background:<?php echo esc_attr( $value ); ?>" aria-hidden="true"></span>
								<?php endif; ?>
								<code><?php echo esc_html( $value ); ?></code>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</details>
		</section>
	</div>
	<?php
}
