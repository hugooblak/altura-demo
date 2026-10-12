<?php
/**
 * Plugin Name: ALTURA Properties
 * Description: The Properties the agency edits, the slider hero and property grid that read from them, the enquiry form, the enquiry inbox with CSV export, and one settings screen for the tracking IDs. Built for the ALTURA demo.
 * Version: 1.0.0
 * Requires at least: 6.6
 * Requires PHP: 7.4
 * Author: Hugo Oblak
 * License: GPL-2.0-or-later
 * Text Domain: altura-leads
 *
 * Why a plugin and not theme code: properties and enquiries are business data. They must survive
 * a redesign, or a new theme in two years takes the whole portfolio with it.
 *
 * @package ALTURAProperties
 */

defined( 'ABSPATH' ) || exit;

define( 'NPL_VERSION', '1.0.0' );
define( 'NPL_DIR', __DIR__ );
define( 'NPL_URL', plugin_dir_url( __FILE__ ) );

require NPL_DIR . '/includes/property-post-type.php'; // Properties: what the agency edits.
require NPL_DIR . '/includes/fields.php';             // One place that lists the form's questions.
require NPL_DIR . '/includes/settings.php';           // Settings > ALTURA: email and tracking IDs.
require NPL_DIR . '/includes/tracking.php';           // Analytics, Pixel and Search Console, off until filled in.
require NPL_DIR . '/includes/lead-post-type.php';     // The "Enquiries" inbox in the admin.
require NPL_DIR . '/includes/submit.php';             // Validates and saves a submitted form.
require NPL_DIR . '/includes/export.php';             // "Export CSV" button on the enquiries screen.
require NPL_DIR . '/includes/thank-you.php';          // Keeps the thank-you page out of search results.
require NPL_DIR . '/includes/privacy.php';            // Export or erase a person's enquiries with WordPress's privacy tools.

add_action(
	'init',
	function () {
		foreach ( array( 'hero', 'properties', 'property-facts', 'lead-form' ) as $block ) {
			register_block_type( NPL_DIR . '/blocks/' . $block );
		}
	}
);
