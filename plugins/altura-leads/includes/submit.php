<?php
/**
 * Handles a submitted enquiry form.
 *
 * Flow: validate -> check for bots -> save the enquiry -> email the owner -> send the visitor to
 * the thank-you page. If something is missing, the visitor goes back to the form with their
 * answers kept and clear error messages.
 *
 * Spam protection without a captcha, on purpose: a captcha costs real customers, and a nonce
 * expires and breaks on cached pages, which silently loses real enquiries. Instead we use a
 * hidden "honeypot" field, a signed timestamp (bots submit instantly), and a per-visitor limit.
 *
 * Nothing is ever thrown away: anything that looks like a bot is saved with the status
 * "Suspected spam", with no email alert. If a real person trips a check, the enquiry is still
 * in the inbox.
 *
 * @package ALTURAProperties
 */

defined( 'ABSPATH' ) || exit;

add_action( 'admin_post_nopriv_npl_submit', 'npl_handle_submit' );
add_action( 'admin_post_npl_submit', 'npl_handle_submit' );

/**
 * Signed timestamp placed in the form when it is shown.
 *
 * @return string
 */
function npl_form_token() {
	$time = time();
	return $time . '.' . hash_hmac( 'sha256', (string) $time, wp_salt( 'nonce' ) );
}

/**
 * True if the token is genuine and the form was open for at least 3 seconds.
 * No maximum age: a cached page can be days old and still carry a real person's enquiry.
 *
 * @param string $token Token from the form.
 * @return bool
 */
function npl_token_ok( $token ) {
	$parts = explode( '.', (string) $token );
	if ( 2 !== count( $parts ) ) {
		return false;
	}
	list( $time, $sig ) = $parts;
	if ( ! hash_equals( hash_hmac( 'sha256', $time, wp_salt( 'nonce' ) ), $sig ) ) {
		return false;
	}
	return time() - (int) $time >= 3;
}

/**
 * Check a mobile number and keep it as the person typed it.
 *
 * Deliberately loose. Buyers of these properties live everywhere, and a strict one-country rule would
 * turn people away. We only need to be able to ring it back, so we check it is a plausible
 * international number and leave the rest alone.
 *
 * @param string $raw What the visitor typed.
 * @return array{0: string, 1: bool} The number as typed with tidy spacing, and whether it looks usable.
 */
function npl_clean_phone( $raw ) {
	$tidy   = trim( preg_replace( '/\s+/', ' ', (string) $raw ) );
	$digits = preg_replace( '/\D/', '', $tidy );
	$ok     = strlen( $digits ) >= 9 && strlen( $digits ) <= 15;
	return array( $tidy, $ok );
}

/**
 * Today's date in the site's own timezone, as Y-m-d.
 *
 * @return string
 */
function npl_today() {
	return wp_date( 'Y-m-d' );
}

/**
 * Validate the posted answers.
 *
 * @param array $in Raw input.
 * @return array{0: array, 1: array} Clean values, errors keyed by field.
 */
function npl_validate( $in ) {
	$choices = npl_choices();

	// The email is kept as typed, not run through sanitize_email: that returns an empty string for
	// a typo like "name@example", and the visitor would get the field back blank.
	$v = array(
		'name'     => sanitize_text_field( wp_unslash( $in['name'] ?? '' ) ),
		'email'    => trim( sanitize_text_field( wp_unslash( $in['email'] ?? '' ) ) ),
		'phone'    => sanitize_text_field( wp_unslash( $in['phone'] ?? '' ) ),
		'property' => sanitize_title( wp_unslash( $in['property'] ?? '' ) ),
		'budget'   => sanitize_title( wp_unslash( $in['budget'] ?? '' ) ),
		'message'  => sanitize_textarea_field( wp_unslash( $in['message'] ?? '' ) ),
	);
	$errors = array();

	if ( mb_strlen( $v['name'] ) < 2 ) {
		$errors['name'] = __( 'Enter your name.', 'altura-leads' );
	}

	if ( ! is_email( $v['email'] ) ) {
		$errors['email'] = __( 'Enter an email address, like name@example.com.', 'altura-leads' );
	} else {
		$v['email'] = sanitize_email( $v['email'] );
	}

	// Telephone is optional. If given, it only has to be a plausible international number.
	if ( '' !== $v['phone'] ) {
		list( $phone, $phone_ok ) = npl_clean_phone( $v['phone'] );
		$v['phone']               = $phone;
		if ( ! $phone_ok ) {
			$errors['phone'] = __( 'Enter a telephone number with the country code, like +44 20 7946 0000, or leave it empty.', 'altura-leads' );
		}
	}

	if ( ! isset( $choices['property'][ $v['property'] ] ) ) {
		$errors['property'] = __( 'Choose a property, or "General enquiry".', 'altura-leads' );
	}
	if ( ! isset( $choices['budget'][ $v['budget'] ] ) ) {
		$v['budget'] = '';
	}

	if ( mb_strlen( $v['message'] ) < 5 ) {
		$errors['message'] = __( 'Write a few words about what you are looking for.', 'altura-leads' );
	}
	if ( mb_strlen( $v['message'] ) > 3000 ) {
		$v['message'] = mb_substr( $v['message'], 0, 3000 );
	}

	return array( $v, $errors );
}

/**
 * Main handler.
 */
function npl_handle_submit() {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- see file comment.
	$in   = $_POST;
	$back = wp_get_referer() ? wp_get_referer() : home_url( '/enquire/' );

	// 1. Validate. On errors, go back with answers kept.
	list( $values, $errors ) = npl_validate( $in );
	if ( $errors ) {
		$key = bin2hex( random_bytes( 8 ) );
		set_transient( 'npl_err_' . $key, array( 'errors' => $errors, 'values' => $values ), 10 * MINUTE_IN_SECONDS );
		wp_safe_redirect( add_query_arg( 'npl', $key, remove_query_arg( 'npl', $back ) ) . '#enquire' );
		exit;
	}

	// 2. Bot checks: hidden field filled, submitted too fast or with a forged token, or more than
	//    10 enquiries from one visitor in 10 minutes. The IP is hashed for the count and never stored.
	$ip_key  = 'npl_rate_' . substr( hash_hmac( 'sha256', $_SERVER['REMOTE_ADDR'] ?? '', wp_salt() ), 0, 20 ); // phpcs:ignore
	$count   = (int) get_transient( $ip_key );
	$suspect = ! empty( $in['company_website'] ) || ! npl_token_ok( $in['npl_token'] ?? '' ) || $count >= 10;
	set_transient( $ip_key, $count + 1, 10 * MINUTE_IN_SECONDS );

	// 3. Save.
	$meta                 = $values;
	$meta['status']       = $suspect ? 'spam' : 'new';
	$meta['consent_at']   = current_time( 'mysql' );
	$meta['consent_text'] = npl_consent_text();

	$lead_id = wp_insert_post(
		array(
			'post_type'   => 'npl_lead',
			'post_status' => 'publish',
			'post_title'  => sprintf(
				/* translators: 1: person's name, 2: property. */
				__( '%1$s · %2$s', 'altura-leads' ),
				$values['name'],
				npl_label( 'property', $values['property'] )
			),
			'meta_input'  => $meta,
		),
		true
	);
	if ( is_wp_error( $lead_id ) ) {
		wp_die( esc_html__( 'Sorry, something went wrong and your enquiry was not saved. Please try again.', 'altura-leads' ), 500 );
	}

	// Suspected spam: kept for review, but no alert.
	if ( $suspect ) {
		// Same page, different flag: only real enquiries fire the conversion event.
		wp_safe_redirect( add_query_arg( 'lead', 'received', npl_thank_you_url() ) );
		exit;
	}

	// 4. Email the agent. Uses WordPress's normal email; on a live site add an SMTP plugin so it is not marked as spam.
	$to   = npl_notify_email();
	$body = sprintf(
		"New enquiry from the website.\n\nName: %s\nEmail: %s\nTelephone: %s\nProperty: %s\nBudget: %s\n\nMessage:\n%s\n\nSent: %s\n\nOpen it in WordPress: %s",
		$values['name'],
		$values['email'],
		$values['phone'] ? $values['phone'] : '—',
		npl_label( 'property', $values['property'] ),
		$values['budget'] ? npl_label( 'budget', $values['budget'] ) : '—',
		$values['message'],
		current_time( 'mysql' ),
		admin_url( 'post.php?post=' . $lead_id . '&action=edit' )
	);
	$sent = wp_mail(
		$to,
		sprintf(
			/* translators: 1: person's name, 2: property. */
			__( 'Enquiry: %1$s, %2$s', 'altura-leads' ),
			$values['name'],
			npl_label( 'property', $values['property'] )
		),
		$body,
		array( 'Reply-To: ' . trim( str_replace( array( '<', '>', '"', ',' ), '', $values['name'] ) ) . ' <' . $values['email'] . '>' )
	);

	// Keep a copy of what was sent, so the demo can show the email even where mail is switched off.
	update_post_meta( $lead_id, 'notify_to', $to );
	update_post_meta( $lead_id, 'notify_body', $body );
	update_post_meta( $lead_id, 'notify_sent', $sent ? 'yes' : 'no' );

	do_action( 'npl_lead_saved', $lead_id, $values ); // Hook for a CRM, Zapier or Slack integration.

	// 5. Thank-you page.
	wp_safe_redirect( add_query_arg( 'lead', 'ok', npl_thank_you_url() ) );
	exit;
	// phpcs:enable
}

/**
 * Errors and kept answers for the form currently being shown, if the visitor was sent back.
 *
 * @return array{errors: array, values: array}
 */
function npl_current_state() {
	static $state = null;
	if ( null !== $state ) {
		return $state;
	}
	$state = array( 'errors' => array(), 'values' => array() );
	$key   = isset( $_GET['npl'] ) ? sanitize_key( $_GET['npl'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( $key ) {
		$saved = get_transient( 'npl_err_' . $key );
		if ( is_array( $saved ) ) {
			$state = $saved;
		}
	}
	return $state;
}
