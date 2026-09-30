<?php
/**
 * Class schedule row.
 *
 * @package PEN
 */

$c     = pen_course();
$start = $c['start'] ? strtotime( $c['start'] ) : 0;
?>
<article <?php post_class( 'pen-class' ); ?>>
	<div class="pen-class__date">
		<?php if ( $start ) : ?>
			<span class="pen-class__month"><?php echo esc_html( date_i18n( 'M', $start ) ); ?></span>
			<span class="pen-class__day"><?php echo esc_html( date_i18n( 'j', $start ) ); ?></span>
			<span class="pen-class__dow"><?php echo esc_html( date_i18n( 'D', $start ) ); ?></span>
		<?php else : ?>
			<span class="pen-class__month"><?php esc_html_e( 'TBA', 'pen' ); ?></span>
		<?php endif; ?>
	</div>
	<div>
		<h3 class="pen-class__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<div class="pen-class__meta">
			<span><?php pen_icon( 'calendar' ); ?><?php echo esc_html( pen_course_dates( $c ) ); ?></span>
			<?php if ( $c['time'] ) : ?>
				<span><?php pen_icon( 'clock' ); ?><?php echo esc_html( $c['time'] ); ?></span>
			<?php endif; ?>
			<?php if ( $c['location'] ) : ?>
				<span><?php pen_icon( 'pin' ); ?><?php echo esc_html( $c['location'] ); ?></span>
			<?php endif; ?>
			<span><?php pen_level_badge( $c ); ?></span>
		</div>
	</div>
	<div class="pen-class__action">
		<?php if ( $c['price'] ) : ?>
			<span class="pen-class__price"><?php echo esc_html( $c['price'] ); ?></span>
		<?php endif; ?>
		<?php pen_seats_badge( $c ); ?>
		<?php pen_register_button( $c, 'pen-btn--sm' ); ?>
	</div>
</article>
