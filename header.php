<?php
/**
 * Site header.
 *
 * @package PEN
 */

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'pen' ); ?></a>

<?php if ( pen_mod( 'pen_announce_enable' ) && pen_mod( 'pen_announce_text' ) ) : ?>
	<div class="pen-announcement" id="pen-announcement" data-key="<?php echo esc_attr( md5( pen_mod( 'pen_announce_text' ) ) ); ?>">
		<div class="pen-container">
			<span>
				<?php echo esc_html( pen_mod( 'pen_announce_text' ) ); ?>
				<?php if ( pen_mod( 'pen_announce_link' ) ) : ?>
					&nbsp;<a href="<?php echo esc_url( pen_link( pen_mod( 'pen_announce_link' ) ) ); ?>"><?php echo esc_html( pen_mod( 'pen_announce_link_text' ) ); ?> &rarr;</a>
				<?php endif; ?>
			</span>
			<button type="button" class="pen-announcement__close" aria-label="<?php esc_attr_e( 'Dismiss announcement', 'pen' ); ?>">&times;</button>
		</div>
	</div>
<?php endif; ?>

<header class="pen-header" id="masthead">
	<div class="pen-container pen-header__inner">
		<a class="pen-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
			<?php
			if ( has_custom_logo() ) {
				$logo_id = get_theme_mod( 'custom_logo' );
				echo wp_get_attachment_image( $logo_id, 'full', false, array( 'class' => 'custom-logo', 'alt' => get_bloginfo( 'name' ) ) );
			} else {
				?>
				<span class="pen-brand__mark" aria-hidden="true">PEN</span>
				<span class="pen-brand__text">
					<span class="pen-brand__name"><?php bloginfo( 'name' ); ?></span>
					<?php if ( pen_mod( 'pen_brand_tagline' ) ) : ?>
						<span class="pen-brand__tag"><?php echo esc_html( pen_mod( 'pen_brand_tagline' ) ); ?></span>
					<?php endif; ?>
				</span>
			<?php } ?>
		</a>

		<nav class="pen-nav" id="pen-nav" aria-label="<?php esc_attr_e( 'Primary', 'pen' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'fallback_cb'    => 'pen_menu_fallback',
					'depth'          => 2,
				)
			);
			?>
			<?php if ( pen_mod( 'pen_header_cta_text' ) ) : ?>
				<div class="pen-nav__cta">
					<a class="pen-btn" href="<?php echo esc_url( pen_link( pen_mod( 'pen_header_cta_url' ) ) ); ?>"><?php echo esc_html( pen_mod( 'pen_header_cta_text' ) ); ?></a>
				</div>
			<?php endif; ?>
		</nav>

		<div class="pen-header__actions">
			<button type="button" class="pen-icon-link pen-search-toggle" aria-controls="pen-search-panel" aria-expanded="false">
				<span class="screen-reader-text"><?php esc_html_e( 'Search', 'pen' ); ?></span>
				<?php pen_icon( 'search' ); ?>
			</button>
			<?php if ( pen_has_woo() ) : ?>
				<a class="pen-icon-link" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">
					<span class="screen-reader-text"><?php esc_html_e( 'My account', 'pen' ); ?></span>
					<?php pen_icon( 'user' ); ?>
				</a>
				<a class="pen-icon-link" href="<?php echo esc_url( wc_get_cart_url() ); ?>">
					<span class="screen-reader-text"><?php esc_html_e( 'Cart', 'pen' ); ?></span>
					<?php pen_icon( 'cart' ); ?>
					<?php pen_cart_count(); ?>
				</a>
			<?php endif; ?>
			<?php if ( pen_mod( 'pen_header_cta_text' ) ) : ?>
				<a class="pen-btn pen-btn--sm pen-header__cta" href="<?php echo esc_url( pen_link( pen_mod( 'pen_header_cta_url' ) ) ); ?>"><?php echo esc_html( pen_mod( 'pen_header_cta_text' ) ); ?></a>
			<?php endif; ?>
			<button type="button" class="pen-icon-link pen-menu-toggle" aria-controls="pen-nav" aria-expanded="false">
				<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'pen' ); ?></span>
				<?php pen_icon( 'menu' ); ?>
			</button>
		</div>
	</div>

	<div class="pen-search-panel" id="pen-search-panel" hidden>
		<div class="pen-container"><?php get_search_form(); ?></div>
	</div>
</header>

<main id="main" class="pen-main">
