<?php
/**
 * Home: stats / trust strip.
 *
 * @package PEN
 */

if ( ! pen_mod( 'pen_stats_enable' ) ) {
	return;
}
?>
<section class="pen-stats" aria-label="<?php esc_attr_e( 'By the numbers', 'pen' ); ?>">
	<div class="pen-container pen-stats__grid">
		<?php for ( $i = 1; $i <= 4; $i++ ) : ?>
			<?php if ( pen_mod( "pen_stat{$i}_num" ) ) : ?>
				<div class="pen-stat">
					<span class="pen-stat__num"><?php echo esc_html( pen_mod( "pen_stat{$i}_num" ) ); ?></span>
					<span class="pen-stat__label"><?php echo esc_html( pen_mod( "pen_stat{$i}_label" ) ); ?></span>
				</div>
			<?php endif; ?>
		<?php endfor; ?>
	</div>
</section>
