<?php
/**
 * Connects leads to WordPress's own privacy tools (Tools → Export Personal Data / Erase Personal Data),
 * so a "send me my data" or "delete my data" request is handled in a few clicks.
 *
 * @package ALTURAProperties
 */

defined( 'ABSPATH' ) || exit;

/**
 * Leads that belong to an email address.
 *
 * @param string $email Email address.
 * @return WP_Post[]
 */
function npl_leads_by_email( $email ) {
	return get_posts(
		array(
			'post_type'   => 'npl_lead',
			'post_status' => 'any',
			'numberposts' => -1,
			'meta_key'    => 'email', // phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_value'  => $email, // phpcs:ignore WordPress.DB.SlowDBQuery
		)
	);
}

add_filter(
	'wp_privacy_personal_data_exporters',
	function ( $exporters ) {
		$exporters['altura-leads'] = array(
			'exporter_friendly_name' => __( 'Enquiries', 'altura-leads' ),
			'callback'               => function ( $email ) {
				$items = array();
				foreach ( npl_leads_by_email( $email ) as $lead ) {
					$data = array( array( 'name' => __( 'Received', 'altura-leads' ), 'value' => $lead->post_date ) );
					foreach ( array( 'name', 'email', 'phone', 'property', 'budget', 'message', 'consent_at', 'consent_text' ) as $key ) {
						$value = get_post_meta( $lead->ID, $key, true );
						if ( '' !== $value ) {
							$data[] = array( 'name' => $key, 'value' => $value );
						}
					}
					$items[] = array(
						'group_id'    => 'altura-leads',
						'group_label' => __( 'Enquiries', 'altura-leads' ),
						'item_id'     => 'lead-' . $lead->ID,
						'data'        => $data,
					);
				}
				return array( 'data' => $items, 'done' => true );
			},
		);
		return $exporters;
	}
);

add_filter(
	'wp_privacy_personal_data_erasers',
	function ( $erasers ) {
		$erasers['altura-leads'] = array(
			'eraser_friendly_name' => __( 'Enquiries', 'altura-leads' ),
			'callback'             => function ( $email ) {
				$removed = false;
				foreach ( npl_leads_by_email( $email ) as $lead ) {
					$removed = (bool) wp_delete_post( $lead->ID, true ) || $removed;
				}
				return array(
					'items_removed'  => $removed,
					'items_retained' => false,
					'messages'       => array(),
					'done'           => true,
				);
			},
		);
		return $erasers;
	}
);
