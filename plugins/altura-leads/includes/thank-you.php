<?php
/**
 * Housekeeping for the thank-you page.
 *
 * @package ALTURAProperties
 */

defined( 'ABSPATH' ) || exit;

/**
 * Keep the thank-you page out of Google. Otherwise people land on it from search,
 * and the owner cannot tell a real enquiry from a stray visit.
 */
add_filter(
	'wp_robots',
	function ( $robots ) {
		$thanks = (int) get_option( 'npl_thank_you_page' );
		if ( $thanks && is_page( $thanks ) ) {
			$robots['noindex'] = true;
			$robots['follow']  = true;
		}
		return $robots;
	}
);
