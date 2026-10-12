<?php
/**
 * Plugin Name: ALTURA Demo Notes
 * Description: Portfolio helper. Shows a "this is a demo" bar and, when switched on, a note above each section explaining the design decision behind it. Delete this plugin on a real site.
 * Version: 1.0.0
 * Requires at least: 6.6
 * Author: Hugo Oblak
 * License: GPL-2.0-or-later
 *
 * The notes are not stored in page content, so editors never see them and they vanish with the plugin.
 *
 * @package ALTURADemoNotes
 */

defined( 'ABSPATH' ) || exit;

/**
 * Notes on or off. ?notes=1 turns them on (and remembers it in a cookie while you browse), ?notes=0 turns them off.
 */
function npd_notes_on() {
	static $on = null;
	if ( null === $on ) {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['notes'] ) ) {
			$on = '1' === $_GET['notes'];
		} else {
			$on = isset( $_COOKIE['npd_notes'] ) && '1' === $_COOKIE['npd_notes'];
		}
		// phpcs:enable
	}
	return $on;
}

add_action(
	'send_headers',
	function () {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['notes'] ) && ! headers_sent() ) {
			setcookie( 'npd_notes', npd_notes_on() ? '1' : '0', 0, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN, is_ssl(), true );
		}
	}
);

/**
 * The notes, keyed by the class or block name they sit above.
 *
 * @return array
 */
function npd_notes() {
	return array(
		'wp-block-altura-hero' => array(
			'One screen, six properties, no autoplay',
			'The slider reads the first six properties that have a photo, so the agent changes the hero by editing a property, not the page. Wipe transitions use clip-path and transform only, so they stay smooth on a phone. It does not move by itself: moving content nobody asked for is a problem for many people, and the buttons and the arrow keys do the job.',
		),
		'philo'                => array(
			'The belief, in one sentence',
			'A luxury buyer is not comparing square metres yet. This section says what the agency stands for and shows three photographs that carry the mood. The cards scroll sideways on a phone and are reachable by keyboard.',
		),
		'props'                => array(
			'Properties come from the admin, not from the page',
			'A property is its own content type: place, price, status, bedrooms, bathrooms, area, photo and focus point. Change a price under Properties and it changes on the Properties page, on the property page and in the property list. Typed into each page instead, the same price would live in several places and be wrong in some of them within a month.',
		),
		'enq'                  => array(
			'A short form for a high-value enquiry',
			'Name, email, property and a message are required. Telephone and budget are optional, because a buyer at this level does not want to hand over a number to look at a house. The property arrives already chosen when the visitor comes from a property page. No captcha: a hidden field, a signed timestamp and a rate limit do the work, and anything suspect is saved as "Suspected spam" rather than thrown away.',
		),
		'dark-page'            => array(
			'Inner pages keep the same two tones',
			'Cream for browsing, near-black for the pages where the visitor commits to something: reading the philosophy, enquiring. It keeps the site feeling like one place without a third colour.',
		),
		'prop-single'          => array(
			'A property page that answers the first questions',
			'Place, price and status sit at the top of the facts column, with the numbers a buyer filters on directly below. A sold property keeps its page and swaps the enquiry button for a note and an "Ask about similar" button, so a link shared months ago still works.',
		),
	);
}

/**
 * Put a note in front of each matching section when notes are on.
 */
add_filter(
	'render_block',
	function ( $html, $block ) {
		if ( is_admin() || ! npd_notes_on() ) {
			return $html;
		}
		$classes = preg_split( '/\s+/', (string) ( $block['attrs']['className'] ?? '' ) );
		if ( 'altura/hero' === $block['blockName'] ) {
			$classes[] = 'wp-block-altura-hero';
		}
		foreach ( npd_notes() as $class => $note ) {
			if ( in_array( $class, $classes, true ) && ( 'core/group' === $block['blockName'] || 'altura/hero' === $block['blockName'] ) ) {
				return sprintf(
					'<aside class="npd-note" aria-label="%s"><p class="npd-note-title"><span>Design note</span> %s</p><p>%s</p></aside>',
					esc_attr( 'Design note: ' . $note[0] ),
					esc_html( $note[0] ),
					esc_html( $note[1] )
				) . $html;
			}
		}
		return $html;
	},
	10,
	2
);

/**
 * Demo bar at the very top of every page.
 *
 * It sits above the header, which would otherwise put its link ahead of WordPress's skip link in the
 * tab order. So we print our own skip link first and hide core's.
 */
add_action(
	'wp_body_open',
	function () {
		printf(
			'<a class="npd-skip skip-link screen-reader-text" href="#wp--skip-link--target">%s</a>',
			esc_html__( 'Skip to content', 'altura-demo-notes' )
		);
	},
	0
);

add_action(
	'wp_body_open',
	function () {
		$toggle = add_query_arg( 'notes', npd_notes_on() ? '0' : '1' );
		?>
		<div class="npd-bar" role="region" aria-label="Demo notice">
			<p><strong>Portfolio demo.</strong> ALTURA is a fictional agency. The name, properties, prices and photographs are all invented, and nothing here can be bought.
				<a href="<?php echo esc_url( $toggle ); ?>"><?php echo npd_notes_on() ? 'Hide the design notes' : 'Show the design notes behind each section'; ?></a>
			</p>
		</div>
		<?php
	},
	1
);

add_action(
	'wp_head',
	function () {
		?>
		<style id="npd-css">
			.npd-bar ~ a.skip-link:not(.npd-skip){display:none !important}
			.npd-bar{background:#000;color:#EEE9E2;font-size:.8125rem;line-height:1.5;padding:.55rem 1rem;text-align:center;position:relative;z-index:50}
			.npd-bar p{margin:0}.npd-bar a{color:#D9BE9B;font-weight:500;margin-left:.35rem}
			.npd-note{box-sizing:border-box;margin:0;padding:.9rem clamp(1rem,7.9%,10rem);background:#FFF8DB;border-top:2px dashed #C99A06;border-bottom:2px dashed #C99A06;color:#3D2E00;font-size:.9375rem;line-height:1.55}
			.npd-note p{margin:0;max-width:90ch}.npd-note-title{font-weight:700;margin-bottom:.25rem !important}
			.npd-note-title span{display:inline-block;background:#3D2E00;color:#FFF8DB;font-size:.6875rem;letter-spacing:.06em;text-transform:uppercase;padding:.1rem .4rem;margin-right:.35rem;vertical-align:.1em}
		</style>
		<?php
	}
);

/**
 * A short guide on the Dashboard for anyone reviewing the demo in WordPress Playground.
 */
add_action(
	'wp_dashboard_setup',
	function () {
		wp_add_dashboard_widget(
			'npd_guide',
			'ALTURA demo: where to look',
			function () {
				$first = get_posts( array( 'post_type' => 'al_property', 'numberposts' => 1, 'orderby' => 'menu_order', 'order' => 'ASC' ) );
				$items = array(
					array( home_url( '/?notes=1' ), 'Home page with the design notes switched on' ),
					array( home_url( '/properties/' ), 'The Properties page (price and key facts)' ),
					$first ? array( get_permalink( $first[0] ), 'A property page' ) : null,
					array( home_url( '/enquire/' ), 'The enquiry form (send it, then look at Enquiries)' ),
					array( admin_url( 'edit.php?post_type=al_property' ), 'Properties: change a price and watch every page follow' ),
					array( admin_url( 'edit.php?post_type=npl_lead' ), 'Enquiries: status, the email that was sent, CSV export' ),
					array( admin_url( 'options-general.php?page=altura' ), 'Settings > ALTURA: enquiry email and tracking IDs' ),
					array( admin_url( 'post.php?post=' . get_option( 'page_on_front' ) . '&action=edit' ), 'Edit the home page (normal blocks, no page builder)' ),
					array( admin_url( 'site-editor.php' ), 'Site editor: styles, header, footer, templates' ),
				);
				echo '<ol>';
				foreach ( array_filter( $items ) as $item ) {
					printf( '<li><a href="%s">%s</a></li>', esc_url( $item[0] ), esc_html( $item[1] ) );
				}
				echo '</ol>';
			}
		);
	}
);
