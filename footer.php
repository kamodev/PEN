<?php
/**
 * Site footer.
 *
 * @package PEN
 */

?>
</main>

<?php
// An Elementor Pro Theme Builder footer replaces the theme footer when one applies.
if ( ! ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( 'footer' ) ) ) {
	get_template_part( 'template-parts/footer/' . ( pen_is_minimal_header() ? 'minimal' : 'site-footer' ) );
}
?>

<button type="button" class="pen-to-top" aria-label="<?php esc_attr_e( 'Back to top', 'pen' ); ?>"><?php pen_icon( 'up' ); ?></button>

<?php wp_footer(); ?>
</body>
</html>
