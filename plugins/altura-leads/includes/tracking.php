<?php
/**
 * Google Analytics, Meta Pixel and Search Console.
 *
 * All three are switched off until an ID is typed into Settings > ALTURA. That is why this
 * demo loads no third-party scripts at all: the boxes are empty. Fill one in and only that one
 * starts loading.
 *
 * The conversion event fires once, on the thank-you page, after a real enquiry. Suspected spam
 * never reaches that page with the flag set, so the numbers stay honest.
 *
 * @package ALTURAProperties
 */

defined( 'ABSPATH' ) || exit;

/**
 * Search Console verification tag.
 */
add_action(
	'wp_head',
	function () {
		$code = trim( (string) get_option( 'al_gsc' ) );
		if ( $code ) {
			printf( '<meta name="google-site-verification" content="%s">' . "\n", esc_attr( $code ) );
		}
	},
	1
);

/**
 * Google Analytics 4.
 */
add_action(
	'wp_head',
	function () {
		$id = trim( (string) get_option( 'al_ga4' ) );
		if ( ! preg_match( '/^G-[A-Z0-9]{6,}$/i', $id ) ) {
			return;
		}
		printf( '<script async src="https://www.googletagmanager.com/gtag/js?id=%s"></script>' . "\n", esc_attr( rawurlencode( $id ) ) );
		printf(
			'<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag("js",new Date());gtag("config",%s);</script>' . "\n",
			wp_json_encode( $id )
		);
	},
	3
);

/**
 * Meta Pixel.
 */
add_action(
	'wp_head',
	function () {
		$id = preg_replace( '/\D/', '', (string) get_option( 'al_pixel' ) );
		if ( strlen( $id ) < 15 ) {
			return;
		}
		printf(
			'<script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version="2.0";n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,"script","https://connect.facebook.net/en_US/fbevents.js");fbq("init",%s);fbq("track","PageView");</script>' . "\n",
			wp_json_encode( $id )
		);
	},
	4
);

/**
 * One conversion event on the thank-you page, for whichever of the two is switched on.
 */
add_action(
	'wp_footer',
	function () {
		$thanks = (int) get_option( 'npl_thank_you_page' );
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- reading a flag we set on our own redirect.
		if ( ! $thanks || ! is_page( $thanks ) || ! isset( $_GET['lead'] ) || 'ok' !== $_GET['lead'] ) {
			return;
		}
		$ga    = preg_match( '/^G-[A-Z0-9]{6,}$/i', trim( (string) get_option( 'al_ga4' ) ) );
		$pixel = strlen( preg_replace( '/\D/', '', (string) get_option( 'al_pixel' ) ) ) >= 15;
		if ( ! $ga && ! $pixel ) {
			return;
		}
		echo '<script>';
		if ( $ga ) {
			echo 'if(window.gtag){gtag("event","generate_lead",{currency:"EUR",value:0});}';
		}
		if ( $pixel ) {
			echo 'if(window.fbq){fbq("track","Lead");}';
		}
		echo '</script>' . "\n";
	},
	20
);
