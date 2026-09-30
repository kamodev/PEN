<?php
/**
 * Instructor card.
 *
 * @package PEN
 */

$role  = get_post_meta( get_the_ID(), '_pen_role', true );
$creds = get_post_meta( get_the_ID(), '_pen_credentials', true );
?>
<article <?php post_class( 'pen-card pen-instructor' ); ?>>
	<a class="pen-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php
		if ( has_post_thumbnail() ) {
			the_post_thumbnail( 'pen-square', array( 'alt' => '' ) );
		} else {
			echo '<div class="pen-placeholder">' . pen_get_icon( 'user' ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		?>
	</a>
	<div class="pen-card__body">
		<?php if ( $role ) : ?>
			<span class="pen-instructor__role"><?php echo esc_html( $role ); ?></span>
		<?php endif; ?>
		<h3 class="pen-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<?php if ( $creds ) : ?>
			<div class="pen-instructor__creds"><?php echo esc_html( $creds ); ?></div>
		<?php endif; ?>
	</div>
</article>
