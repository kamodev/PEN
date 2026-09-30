<?php
/**
 * MailPoet integration (https://www.mailpoet.com/).
 *
 * The theme's newsletter band ("Get the Free Family Preparedness Checklist")
 * can feed MailPoet in two ways, chosen under Theme Settings → Integrations:
 *
 *   1. MailPoet list: the theme's own one-field form subscribes people to a
 *      MailPoet list through MailPoet's API. MailPoet's sign-up confirmation
 *      (double opt-in) and welcome emails apply as usual.
 *   2. MailPoet form: a form built in MailPoet → Forms (for extra fields such
 *      as a first name) replaces the theme's form.
 *
 * A chosen MailPoet form wins over a list, and either wins over the
 * "Form action URL" in the Customizer. MailPoet forms anywhere on the site
 * also pick up the theme's colors, fonts and buttons (can be turned off).
 *
 * @package PEN
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether MailPoet is active.
 *
 * @return bool
 */
function pen_mailpoet_active() {
	return class_exists( '\MailPoet\API\API' );
}

/**
 * MailPoet's public API, or null.
 *
 * @return object|null
 */
function pen_mailpoet_api() {
	if ( ! pen_mailpoet_active() ) {
		return null;
	}
	try {
		return \MailPoet\API\API::MP( 'v1' );
	} catch ( \Exception $e ) {
		return null;
	}
}

/**
 * MailPoet lists.
 *
 * @return array id => name
 */
function pen_mailpoet_lists() {
	$api = pen_mailpoet_api();
	if ( ! $api ) {
		return array();
	}
	try {
		$lists = array();
		foreach ( $api->getLists() as $list ) {
			$lists[ (int) $list['id'] ] = $list['name'];
		}
		return $lists;
	} catch ( \Exception $e ) {
		return array();
	}
}

/**
 * Enabled MailPoet forms.
 *
 * @return array id => name
 */
function pen_mailpoet_forms() {
	global $wpdb;
	if ( ! pen_mailpoet_active() ) {
		return array();
	}
	$table = $wpdb->prefix . 'mailpoet_forms';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery -- MailPoet has no public API for listing forms.
	$rows = $wpdb->get_results( "SELECT id, name FROM {$table} WHERE deleted_at IS NULL AND status = 'enabled' ORDER BY name" );
	$forms = array();
	foreach ( (array) $rows as $row ) {
		$forms[ (int) $row->id ] = $row->name;
	}
	return $forms;
}

/**
 * How the newsletter band uses MailPoet.
 *
 * @return string 'form', 'list' or '' (not used).
 */
function pen_mailpoet_mode() {
	if ( ! pen_mailpoet_active() ) {
		return '';
	}
	if ( pen_setting( 'mailpoet_form' ) && shortcode_exists( 'mailpoet_form' ) ) {
		return 'form';
	}
	return pen_setting( 'mailpoet_list' ) ? 'list' : '';
}

/**
 * Whether MailPoet asks new subscribers to confirm by email.
 *
 * @return bool
 */
function pen_mailpoet_needs_confirmation() {
	if ( class_exists( '\MailPoet\Settings\SettingsController' ) ) {
		try {
			return (bool) \MailPoet\Settings\SettingsController::getInstance()->get( 'signup_confirmation.enabled', true );
		} catch ( \Exception $e ) {
			return true;
		}
	}
	return true;
}

/**
 * Newsletter band form markup when MailPoet is in use.
 *
 * @param string|null $html Markup from an earlier filter, or null.
 * @return string|null
 */
function pen_mailpoet_newsletter_form( $html ) {
	$mode = pen_mailpoet_mode();

	if ( 'form' === $mode ) {
		return '<div class="pen-newsletter pen-newsletter--mailpoet">' . do_shortcode( '[mailpoet_form id="' . absint( pen_setting( 'mailpoet_form' ) ) . '"]' ) . '</div>';
	}

	if ( 'list' !== $mode ) {
		return $html;
	}

	ob_start();
	pen_mailpoet_status_message();
	?>
	<form class="pen-newsletter" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
		<input type="hidden" name="action" value="pen_mailpoet_subscribe">
		<?php wp_referer_field(); ?>
		<?php // Left empty by people; bots that fill every field are ignored. ?>
		<div class="pen-hp" aria-hidden="true">
			<label for="pen-news-website"><?php esc_html_e( 'Leave this field empty', 'pen' ); ?></label>
			<input id="pen-news-website" type="text" name="pen_website" value="" tabindex="-1" autocomplete="off">
		</div>
		<label class="screen-reader-text" for="pen-news-email"><?php esc_html_e( 'Email address', 'pen' ); ?></label>
		<input id="pen-news-email" type="email" name="pen_email" placeholder="<?php esc_attr_e( 'Your email address', 'pen' ); ?>" required autocomplete="email">
		<button type="submit"><?php echo esc_html( pen_mod( 'pen_news_button' ) ); ?></button>
	</form>
	<?php
	return ob_get_clean();
}
add_filter( 'pen_newsletter_form_html', 'pen_mailpoet_newsletter_form' );

/**
 * Result message after the theme form posts to MailPoet.
 */
function pen_mailpoet_status_message() {
	$status = isset( $_GET['pen-subscribed'] ) ? sanitize_key( wp_unslash( $_GET['pen-subscribed'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! $status ) {
		return;
	}
	$messages = array(
		'ok'      => pen_mailpoet_needs_confirmation()
			? __( 'Almost done! Check your inbox and click the link to confirm your subscription.', 'pen' )
			: __( 'You\'re on the list. Watch your inbox for the checklist.', 'pen' ),
		'invalid' => __( 'That email address doesn\'t look right. Please check it and try again.', 'pen' ),
		'wait'    => __( 'Too many sign-ups from your connection. Please try again in a few minutes.', 'pen' ),
		'error'   => __( 'We couldn\'t sign you up just now. Please try again in a few minutes.', 'pen' ),
	);
	if ( ! isset( $messages[ $status ] ) ) {
		return;
	}
	printf(
		'<p class="pen-newsletter__status pen-newsletter__status--%1$s" role="%2$s">%3$s</p>',
		esc_attr( $status ),
		'ok' === $status ? 'status' : 'alert',
		esc_html( $messages[ $status ] )
	);
}

/**
 * Handle the theme form: add the email to the chosen MailPoet list.
 */
function pen_mailpoet_subscribe() {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- public sign-up form on cacheable pages; a honeypot and MailPoet's own checks guard it.
	$redirect = remove_query_arg( 'pen-subscribed', wp_get_referer() ? wp_get_referer() : home_url( '/' ) );
	$email    = isset( $_POST['pen_email'] ) ? sanitize_email( wp_unslash( $_POST['pen_email'] ) ) : '';
	$is_bot   = ! empty( $_POST['pen_website'] );
	// phpcs:enable

	$list = absint( pen_setting( 'mailpoet_list' ) );
	$api  = pen_mailpoet_api();

	if ( $is_bot ) {
		$status = 'ok'; // Don't tell bots they were caught.
	} elseif ( ! pen_mailpoet_rate_ok() ) {
		$status = 'wait';
	} elseif ( ! is_email( $email ) ) {
		$status = 'invalid';
	} elseif ( ! $api || ! $list ) {
		$status = 'error';
	} else {
		$status = 'ok';
		try {
			$api->addSubscriber( array( 'email' => $email ), array( $list ) );
		} catch ( \Exception $e ) {
			$codes = class_exists( '\MailPoet\API\MP\v1\APIException' ) ? array(
				'exists'       => \MailPoet\API\MP\v1\APIException::SUBSCRIBER_EXISTS,
				'confirmation' => \MailPoet\API\MP\v1\APIException::CONFIRMATION_FAILED_TO_SEND,
				'welcome'      => \MailPoet\API\MP\v1\APIException::WELCOME_FAILED_TO_SEND,
			) : array();

			if ( $codes && in_array( $e->getCode(), array( $codes['confirmation'], $codes['welcome'] ), true ) ) {
				// Saved, but MailPoet couldn't send an email. The visitor is signed up; the site owner should check MailPoet's sending setup.
				error_log( 'PEN MailPoet sign-up saved, but an email failed to send: ' . $e->getMessage() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			} elseif ( $codes && $codes['exists'] === $e->getCode() ) {
				// Existing subscriber: add them to the list instead.
				try {
					$subscriber = $api->getSubscriber( $email );
					$api->subscribeToLists( $subscriber['id'], array( $list ) );
				} catch ( \Exception $inner ) {
					if ( ! in_array( $inner->getCode(), array( $codes['confirmation'], $codes['welcome'] ), true ) ) {
						$status = 'error';
					}
				}
			} else {
				$status = 'error';
			}
		}
	}

	wp_safe_redirect( add_query_arg( 'pen-subscribed', $status, $redirect ) . '#newsletter' );
	exit;
}
add_action( 'admin_post_pen_mailpoet_subscribe', 'pen_mailpoet_subscribe' );
add_action( 'admin_post_nopriv_pen_mailpoet_subscribe', 'pen_mailpoet_subscribe' );

/**
 * Limit sign-ups per visitor IP address.
 *
 * The theme form subscribes through MailPoet's API, which skips the CAPTCHA
 * and throttling on MailPoet's own forms. This cap stops a script from making
 * MailPoet send confirmation emails to many addresses. Sites behind a proxy
 * that hides visitor IPs share one counter; raise the limit with the
 * pen_mailpoet_rate_limit filter if needed.
 *
 * @return bool Whether this sign-up may go ahead.
 */
function pen_mailpoet_rate_ok() {
	$limit = (int) apply_filters( 'pen_mailpoet_rate_limit', 5 ); // Sign-ups per IP address ...
	$ip    = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$key   = 'pen_mp_rate_' . md5( $ip );
	$count = (int) get_transient( $key );
	if ( $limit > 0 && $count >= $limit ) {
		return false;
	}
	set_transient( $key, $count + 1, 10 * MINUTE_IN_SECONDS ); // ... per 10 minutes.
	return true;
}

/**
 * Load mailpoet.css when MailPoet is active and matching is on.
 *
 * @param string[] $parts Stylesheet parts.
 * @return string[]
 */
function pen_mailpoet_style_part( $parts ) {
	if ( pen_mailpoet_active() && 'on' === pen_setting( 'mailpoet_match' ) ) {
		$parts[] = 'mailpoet';
	}
	return $parts;
}
add_filter( 'pen_style_parts', 'pen_mailpoet_style_part' );
