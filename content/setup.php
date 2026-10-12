<?php
/**
 * Demo site setup for ALTURA.
 *
 * Creates the pages from the theme's patterns, builds the menu, adds the six properties with
 * their photographs, and drops in three sample enquiries so the inbox is not empty.
 * Safe to run again: it skips anything that already exists.
 *
 * Playground runs this automatically (see blueprint.json).
 * Local run:  wp eval-file content/setup.php
 *
 * @package ALTURA
 */

defined( 'ABSPATH' ) || exit;

wp_set_current_user( 1 ); // Admin, so block markup is saved exactly as written.

require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

/**
 * Pattern markup with nested pattern references expanded, so saved pages hold plain editable blocks.
 *
 * @param string $slug Pattern slug.
 * @return string
 */
function nps_pattern( $slug ) {
	$pattern = WP_Block_Patterns_Registry::get_instance()->get_registered( $slug );
	if ( ! $pattern ) {
		nps_log( "Missing pattern: $slug" );
		return '';
	}
	return preg_replace_callback(
		'#<!-- wp:pattern \{"slug":"([^"]+)"\} /-->#',
		function ( $m ) {
			return nps_pattern( $m[1] );
		},
		$pattern['content']
	);
}

/**
 * Log a line when running under WP-CLI.
 *
 * @param string $msg Message.
 */
function nps_log( $msg ) {
	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		WP_CLI::log( $msg );
	}
}

/**
 * Create a page once.
 *
 * @param string $slug     Page slug.
 * @param string $title    Title.
 * @param string $content  Block markup.
 * @param string $excerpt  Meta description.
 * @param string $template Page template.
 * @return int Page ID.
 */
function nps_page( $slug, $title, $content, $excerpt, $template = '' ) {
	$existing = get_page_by_path( $slug );
	if ( $existing ) {
		return $existing->ID;
	}
	$id = wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_name'    => $slug,
			'post_title'   => $title,
			'post_content' => $content,
			'post_excerpt' => $excerpt,
			'meta_input'   => $template ? array( '_wp_page_template' => $template ) : array(),
		)
	);
	nps_log( "Page: $title" );
	return $id;
}

/**
 * Copy one of the theme's photographs into the media library and return its ID.
 *
 * @param string $file  File name inside the theme's assets/img folder.
 * @param string $title Media title.
 * @param string $alt   Alternative text.
 * @return int Attachment ID, or 0.
 */
function nps_photo( $file, $title, $alt ) {
	$path = get_theme_file_path( 'assets/img/' . $file );
	if ( ! file_exists( $path ) ) {
		nps_log( "Missing photo: $file" );
		return 0;
	}
	$tmp = wp_tempnam( $file );
	copy( $path, $tmp );
	$id = media_handle_sideload( array( 'name' => $file, 'tmp_name' => $tmp ), 0, $title );
	if ( is_wp_error( $id ) ) {
		nps_log( 'Photo failed: ' . $id->get_error_message() );
		return 0;
	}
	update_post_meta( $id, '_wp_attachment_image_alt', $alt );
	return (int) $id;
}

/* --------------------------------------------------------------------------
 * Site settings
 * ----------------------------------------------------------------------- */
update_option( 'blogname', 'ALTURA' );
update_option( 'blogdescription', 'Curated luxury residences in Greece, Spain, France, Portugal, Italy and Morocco' );
update_option( 'timezone_string', 'Europe/London' );
update_option( 'date_format', 'j F Y' );
update_option( 'start_of_week', 1 );
update_option( 'posts_per_page', 9 );

// Where enquiries are emailed. A sample address, labelled as one in the admin.
if ( ! get_option( 'npl_notify_email' ) ) {
	update_option( 'npl_notify_email', 'enquiries@altura.example' );
}
// Analytics, Pixel and Search Console stay empty on purpose: the demo loads no third-party scripts.
foreach ( array( 'al_ga4', 'al_pixel', 'al_gsc' ) as $al_key ) {
	add_option( $al_key, '' );
}

// Remove WordPress's sample content.
foreach ( array( 'hello-world' => 'post', 'sample-page' => 'page' ) as $slug => $type ) {
	$sample = get_page_by_path( $slug, OBJECT, $type );
	if ( $sample ) {
		wp_delete_post( $sample->ID, true );
	}
}
$privacy_draft = get_page_by_path( 'privacy-policy' );
if ( $privacy_draft && 'draft' === $privacy_draft->post_status ) {
	wp_delete_post( $privacy_draft->ID, true );
}

/* --------------------------------------------------------------------------
 * Properties: six invented residences, with the photographs from the theme.
 * ----------------------------------------------------------------------- */
$al_properties = array(
	array(
		'title'   => 'Villa Kastello',
		'slug'    => 'villa-kastello',
		'photo'   => 'hero-1.jpg',
		'alt'     => 'Cantilevered concrete and timber villa with a long stone infinity pool above the sea at sunset, Corfu',
		'place'   => 'Corfu, Greece',
		'price'   => 4850000,
		'status'  => 'available',
		'beds'    => 5,
		'baths'   => 5,
		'size'    => 460,
		'plot'    => 6200,
		'ref'     => 'AL-0101',
		'focus'   => 85,
		'excerpt' => 'A cantilevered house of concrete and timber, held out over the water on the north-east coast.',
		'content' => "The house is built in two long volumes. The lower one holds the kitchen, the dining room and a living space that opens on to a stone terrace and a 20-metre pool. The upper one carries the bedrooms, each with its own view of the sea.\n\nThe plot is terraced and planted with olive and cypress. A private path leads to a landing stage below the house.",
	),
	array(
		'title'   => 'Villa Marea',
		'slug'    => 'villa-marea',
		'photo'   => 'hero-2.jpg',
		'alt'     => 'Whitewashed terraced villa with a pool and cypress trees above a bay at sunset, Mallorca',
		'place'   => 'Mallorca, Spain',
		'price'   => 6200000,
		'status'  => 'available',
		'beds'    => 6,
		'baths'   => 6,
		'size'    => 540,
		'plot'    => 8000,
		'ref'     => 'AL-0102',
		'focus'   => 78,
		'excerpt' => 'Whitewashed terraces step down a hillside to a private bay on the west coast.',
		'content' => "Five terraces follow the slope, each with its own use: a breakfast court, a shaded dining pergola, a pool, a sunken lounge and a path to the water.\n\nInside, thick lime-rendered walls keep the rooms cool through the summer. The main bedroom suite sits at the top of the house, away from the pool.",
	),
	array(
		'title'   => 'Maison Argentière',
		'slug'    => 'maison-argentiere',
		'photo'   => 'hero-3.jpg',
		'alt'     => 'Dark timber and stone alpine chalet with glowing windows and snow-capped peaks at dusk, Chamonix',
		'place'   => 'Chamonix, France',
		'price'   => 7400000,
		'status'  => 'available',
		'beds'    => 5,
		'baths'   => 5,
		'size'    => 410,
		'plot'    => 1900,
		'ref'     => 'AL-0103',
		'focus'   => 82,
		'excerpt' => 'A dark timber and stone chalet at the foot of the glacier, with the mountain in every window.',
		'content' => "The chalet faces the valley and is built to be lived in all year. A double-height living room looks straight at the peaks, and a boot room and ski store open directly to the lane.\n\nThe lower floor has a spa with a sauna and a plunge pool, and a screening room.",
	),
	array(
		'title'   => 'Casa Pedra',
		'slug'    => 'casa-pedra',
		'photo'   => 'hero-4.jpg',
		'alt'     => 'Low rammed-earth and timber pavilion with a shallow pool among pines and dune grass, Comporta',
		'place'   => 'Comporta, Portugal',
		'price'   => 3900000,
		'status'  => 'under-offer',
		'beds'    => 4,
		'baths'   => 4,
		'size'    => 320,
		'plot'    => 12000,
		'ref'     => 'AL-0104',
		'focus'   => 72,
		'excerpt' => 'A low rammed-earth pavilion among pines and dune grass, ten minutes from the beach.',
		'content' => "Casa Pedra is a single storey of rammed earth and timber, arranged around a shallow pool. The rooms open on both sides, so air moves through the house all day.\n\nThe land is mostly pine and cork oak, with a guest studio at the far end of the plot.",
	),
	array(
		'title'   => 'Villa Solenne',
		'slug'    => 'villa-solenne',
		'photo'   => 'hero-5.jpg',
		'alt'     => 'Pale limestone lakeside villa with an infinity pool above still water at dusk, Lake Como',
		'place'   => 'Lake Como, Italy',
		'price'   => 9800000,
		'status'  => 'available',
		'beds'    => 7,
		'baths'   => 7,
		'size'    => 680,
		'plot'    => 5400,
		'ref'     => 'AL-0105',
		'focus'   => 82,
		'excerpt' => 'A pale limestone villa on the eastern shore, with its own boathouse and a pool at the water line.',
		'content' => "The villa was rebuilt in limestone and keeps the proportions of the lakeside houses around it. Tall windows on the water side bring the lake into every principal room.\n\nA terraced garden runs down to the boathouse, which has a mooring for two boats.",
	),
	array(
		'title'   => 'Dar Zitoun',
		'slug'    => 'dar-zitoun',
		'photo'   => 'hero-6.jpg',
		'alt'     => 'Ochre courtyard house with a long pool, olive trees and a lantern-lit arcade at dusk, Marrakech',
		'place'   => 'Marrakech, Morocco',
		'price'   => 0,
		'status'  => 'available',
		'beds'    => 6,
		'baths'   => 6,
		'size'    => 590,
		'plot'    => 9500,
		'ref'     => 'AL-0106',
		'focus'   => 55,
		'excerpt' => 'An ochre courtyard house among olive trees, built around a long reflecting pool.',
		'content' => "Dar Zitoun is built around a central courtyard and a 25-metre pool lined with olive trees. An arcade runs the length of the courtyard and is lit by lanterns in the evening.\n\nThe house is offered by private arrangement. Ask for the details and a viewing.",
	),
);

foreach ( $al_properties as $al_order => $al_p ) {
	$existing = get_page_by_path( $al_p['slug'], OBJECT, 'al_property' );
	if ( $existing ) {
		continue;
	}
	$post_id = wp_insert_post(
		array(
			'post_type'    => 'al_property',
			'post_status'  => 'publish',
			'post_name'    => $al_p['slug'],
			'post_title'   => $al_p['title'],
			'post_excerpt' => $al_p['excerpt'],
			'post_content' => implode(
				"\n\n",
				array_map(
					function ( $para ) {
						return "<!-- wp:paragraph -->\n<p>" . esc_html( $para ) . "</p>\n<!-- /wp:paragraph -->";
					},
					explode( "\n\n", $al_p['content'] )
				)
			),
			'menu_order'   => $al_order,
			'meta_input'   => array(
				'place'  => $al_p['place'],
				'price'  => $al_p['price'],
				'status' => $al_p['status'],
				'beds'   => $al_p['beds'],
				'baths'  => $al_p['baths'],
				'size'   => $al_p['size'],
				'plot'   => $al_p['plot'],
				'ref'    => $al_p['ref'],
				'focus'  => $al_p['focus'],
			),
		)
	);
	if ( $post_id && ! is_wp_error( $post_id ) ) {
		$photo = nps_photo( $al_p['photo'], $al_p['title'], $al_p['alt'] );
		if ( $photo ) {
			set_post_thumbnail( $post_id, $photo );
		}
	}
	nps_log( 'Property: ' . $al_p['title'] );
}

/* --------------------------------------------------------------------------
 * Pages
 * ----------------------------------------------------------------------- */
$home = nps_page(
	'home',
	'Curated luxury residences',
	nps_pattern( 'altura/page-home' ),
	'ALTURA Properties is a fictional agency showing six handpicked residences in Greece, Spain, France, Portugal, Italy and Morocco.',
	'page-landing'
);

nps_page(
	'properties',
	'Properties',
	nps_pattern( 'altura/page-properties' ),
	'Handpicked residences with price, bedrooms and living area. Open one for the full details or enquire about a private viewing.',
	'page-landing'
);

nps_page(
	'philosophy',
	'Our philosophy',
	nps_pattern( 'altura/page-philosophy' ),
	'How ALTURA chooses what it shows: a short list, viewings arranged around the buyer, and discretion for sellers who prefer it.',
	'page-landing'
);

nps_page(
	'enquire',
	'Enquire',
	nps_pattern( 'altura/page-enquire' ),
	'Begin a private conversation. Choose a property, tell us what you are looking for, and a member of the team replies personally.',
	'page-landing'
);

$thanks = nps_page(
	'thank-you',
	'Thank you',
	nps_pattern( 'altura/thank-you' ),
	'Your enquiry has been received.',
	'page-landing'
);

$privacy_content = <<<'HTML'
<!-- wp:paragraph -->
<p><strong>Sample policy for a demo site.</strong> A real policy must be written for the business and checked by someone qualified.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">What we collect</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>When you send an enquiry we collect your name, email address, an optional telephone number and budget range, the property you chose, and the message you write.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">How we use it</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Only to reply to your enquiry and arrange a viewing. We keep a record of the consent you gave when you sent the form. We never sell or share your details, and we do not send marketing unless you ask for it.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">How long we keep it</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Two years from your last message, so we can pick up the conversation if you come back. After that we delete it on request.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Your choices</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Email <a href="mailto:privacy@altura.example">privacy@altura.example</a> to see what we hold or to have it deleted. That address is a sample for this demo.</p>
<!-- /wp:paragraph -->
HTML;
$privacy = nps_page( 'privacy-policy', 'Privacy policy', $privacy_content, 'How ALTURA uses the details you send with the enquiry form.' );
update_option( 'wp_page_for_privacy_policy', $privacy );

update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $home );
update_option( 'npl_thank_you_page', $thanks );

/* --------------------------------------------------------------------------
 * Main menu (Appearance > Editor > Navigation). Used by the header and the footer.
 * ----------------------------------------------------------------------- */
if ( ! get_posts( array( 'post_type' => 'wp_navigation', 'title' => 'Main menu', 'numberposts' => 1, 'post_status' => 'publish' ) ) ) {
	$nav = '';
	foreach ( array( array( 'Properties', '/properties/' ), array( 'Philosophy', '/philosophy/' ), array( 'Contact', '/enquire/' ) ) as $item ) {
		$page  = get_page_by_path( trim( $item[1], '/' ) );
		$attrs = array(
			'label' => $item[0],
			'url'   => home_url( $item[1] ),
			'kind'  => $page ? 'post-type' : 'custom',
		);
		if ( $page ) {
			$attrs['id']   = $page->ID;
			$attrs['type'] = 'page';
		}
		$nav .= '<!-- wp:navigation-link ' . wp_json_encode( $attrs, JSON_UNESCAPED_SLASHES ) . ' /-->';
	}
	wp_insert_post(
		array(
			'post_type'    => 'wp_navigation',
			'post_status'  => 'publish',
			'post_title'   => 'Main menu',
			'post_content' => $nav,
		)
	);
	nps_log( 'Menu created.' );
}

/* --------------------------------------------------------------------------
 * Three sample enquiries, so the inbox shows what real ones look like.
 * ----------------------------------------------------------------------- */
if ( post_type_exists( 'npl_lead' ) && ! get_posts( array( 'post_type' => 'npl_lead', 'numberposts' => 1 ) ) ) {
	$samples = array(
		array( 'Sample buyer A', 'sample-a@example.com', '+44 20 7946 0001', 'villa-solenne', '5-10m', 'We would like to see the house in the second half of June, with two children. Is the boathouse included in the price?', 'viewing', '-2 days' ),
		array( 'Sample buyer B', 'sample-b@example.com', '', 'general', '2-5m', 'Looking for something near the sea in southern Europe, quiet, with a pool. Open on country.', 'new', '-5 hours' ),
		array( 'Sample buyer C', 'sample-c@example.com', '+33 1 70 18 99 02', 'dar-zitoun', '', 'Please send the details and the asking price.', 'new', '-40 minutes' ),
	);
	foreach ( $samples as $s ) {
		wp_insert_post(
			array(
				'post_type'   => 'npl_lead',
				'post_status' => 'publish',
				'post_title'  => $s[0] . ' · ' . npl_label( 'property', $s[3] ),
				'post_date'   => wp_date( 'Y-m-d H:i:s', strtotime( $s[7] ) ),
				'meta_input'  => array(
					'name'         => $s[0],
					'email'        => $s[1],
					'phone'        => $s[2],
					'property'     => $s[3],
					'budget'       => $s[4],
					'message'      => $s[5],
					'status'       => $s[6],
					'consent_at'   => wp_date( 'Y-m-d H:i:s', strtotime( $s[7] ) ),
					'consent_text' => npl_consent_text(),
				),
			)
		);
	}
	nps_log( 'Sample enquiries added.' );
}

// Pretty links (/properties/ instead of ?page_id=5). Flushed last, after every page exists.
// set_permalink_structure() also updates the rules WordPress already loaded for this request.
// Only saving the option would rebuild the old rules, and pages would return "not found".
global $wp_rewrite;
$wp_rewrite->set_permalink_structure( '/%postname%/' );
// The property type was registered before the structure changed, so add its rules again.
$nps_type = get_post_type_object( 'al_property' );
if ( $nps_type ) {
	$nps_type->add_rewrite_rules();
}
flush_rewrite_rules( false );

nps_log( 'ALTURA demo ready.' );
