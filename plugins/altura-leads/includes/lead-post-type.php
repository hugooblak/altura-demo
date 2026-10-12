<?php
/**
 * The "Enquiries" inbox: a private post type, list columns, a details box and a status field.
 *
 * @package ALTURAProperties
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'init',
	function () {
		register_post_type(
			'npl_lead',
			array(
				'labels'          => array(
					'name'          => __( 'Enquiries', 'altura-leads' ),
					'singular_name' => __( 'Enquiry', 'altura-leads' ),
					'edit_item'     => __( 'Enquiry', 'altura-leads' ),
					'search_items'  => __( 'Search enquiries', 'altura-leads' ),
					'not_found'     => __( 'No enquiries yet. Send the form on the Enquire page to see one here.', 'altura-leads' ),
					'all_items'     => __( 'All enquiries', 'altura-leads' ),
				),
				'public'          => false, // Never visible on the front end or in search.
				'show_ui'         => true,
				'show_in_rest'    => false, // Keeps lead data out of the public REST API.
				'menu_icon'       => 'dashicons-email-alt',
				'menu_position'   => 3,
				'supports'        => array( 'title' ),
				// Own permissions, so only Administrators and Editors see leads (not Authors or Contributors).
				'capability_type' => array( 'npl_lead', 'npl_leads' ),
				'capabilities'    => array( 'create_posts' => 'do_not_allow' ), // Requests only come from the form.
				'map_meta_cap'    => true,
			)
		);
	}
);

/**
 * Give Administrators and Editors the lead permissions. Runs once, then remembers it.
 */
add_action(
	'init',
	function () {
		if ( get_option( 'npl_caps_version' ) === NPL_VERSION ) {
			return;
		}
		$caps = array( 'edit_npl_leads', 'edit_others_npl_leads', 'edit_published_npl_leads', 'read_private_npl_leads', 'delete_npl_leads', 'delete_others_npl_leads', 'delete_published_npl_leads', 'publish_npl_leads', 'export_npl_leads' );
		foreach ( array( 'administrator', 'editor' ) as $role_name ) {
			$role = get_role( $role_name );
			if ( $role ) {
				foreach ( $caps as $cap ) {
					$role->add_cap( $cap );
				}
			}
		}
		update_option( 'npl_caps_version', NPL_VERSION );
	},
	5
);

/**
 * Columns on the Enquiries list.
 */
add_filter(
	'manage_npl_lead_posts_columns',
	function () {
		return array(
			'cb'           => '<input type="checkbox" />',
			'title'        => __( 'Name', 'altura-leads' ),
			'npl_property' => __( 'Property', 'altura-leads' ),
			'npl_budget'   => __( 'Budget', 'altura-leads' ),
			'npl_email'    => __( 'Email', 'altura-leads' ),
			'npl_phone'    => __( 'Telephone', 'altura-leads' ),
			'npl_status'   => __( 'Status', 'altura-leads' ),
			'date'         => __( 'Received', 'altura-leads' ),
		);
	}
);

add_action(
	'manage_npl_lead_posts_custom_column',
	function ( $column, $post_id ) {
		switch ( $column ) {
			case 'npl_property':
				echo esc_html( npl_label( 'property', get_post_meta( $post_id, 'property', true ) ) );
				break;
			case 'npl_budget':
				echo esc_html( npl_label( 'budget', get_post_meta( $post_id, 'budget', true ) ) );
				break;
			case 'npl_email':
				$email = get_post_meta( $post_id, 'email', true );
				printf( '<a href="mailto:%s">%s</a>', esc_attr( $email ), esc_html( $email ) );
				break;
			case 'npl_phone':
				$phone = get_post_meta( $post_id, 'phone', true );
				if ( $phone ) {
					printf( '<a href="tel:%s">%s</a>', esc_attr( preg_replace( '/[^\d+]/', '', $phone ) ), esc_html( $phone ) );
				}
				break;
			case 'npl_status':
				$statuses = npl_statuses();
				$status   = get_post_meta( $post_id, 'status', true ) ?: 'new';
				echo esc_html( $statuses[ $status ] ?? $status );
				break;
		}
	},
	10,
	2
);

/**
 * Details box on a single lead, plus the status dropdown.
 */
add_action(
	'add_meta_boxes_npl_lead',
	function () {
		add_meta_box( 'npl_details', __( 'What they told us', 'altura-leads' ), 'npl_details_box', 'npl_lead', 'normal', 'high' );
		add_meta_box( 'npl_email', __( 'The email sent to the agent', 'altura-leads' ), 'npl_email_box', 'npl_lead', 'normal', 'default' );
		add_meta_box( 'npl_status', __( 'Follow-up status', 'altura-leads' ), 'npl_status_box', 'npl_lead', 'side', 'high' );
	}
);

/**
 * Read-only table of everything the form captured.
 *
 * @param WP_Post $post Lead.
 */
function npl_details_box( $post ) {
	$rows = array(
		__( 'Name', 'altura-leads' )      => get_post_meta( $post->ID, 'name', true ),
		__( 'Email', 'altura-leads' )     => get_post_meta( $post->ID, 'email', true ),
		__( 'Telephone', 'altura-leads' ) => get_post_meta( $post->ID, 'phone', true ),
		__( 'Property', 'altura-leads' )  => npl_label( 'property', get_post_meta( $post->ID, 'property', true ) ),
		__( 'Budget', 'altura-leads' )    => npl_label( 'budget', get_post_meta( $post->ID, 'budget', true ) ),
		__( 'Message', 'altura-leads' )   => get_post_meta( $post->ID, 'message', true ),
	);
	$rows[ __( 'Consent given', 'altura-leads' ) ] = get_post_meta( $post->ID, 'consent_at', true ) . ' — "' . get_post_meta( $post->ID, 'consent_text', true ) . '"';
	$rows = array_filter( $rows, function ( $value ) { return '' !== trim( (string) $value ); } );

	echo '<table class="widefat striped"><tbody>';
	foreach ( $rows as $label => $value ) {
		printf( '<tr><th style="width:160px">%s</th><td>%s</td></tr>', esc_html( $label ), esc_html( $value ) );
	}
	echo '</tbody></table>';
}

/**
 * The notification email, exactly as it was sent. Useful when mail is switched off,
 * for example in WordPress Playground, where nothing can actually leave the browser.
 *
 * @param WP_Post $post Request.
 */
function npl_email_box( $post ) {
	$to   = get_post_meta( $post->ID, 'notify_to', true );
	$body = get_post_meta( $post->ID, 'notify_body', true );
	if ( ! $body ) {
		echo '<p>' . esc_html__( 'No email was sent for this one. It is either a sample enquiry from the demo content or was flagged as suspected spam.', 'altura-leads' ) . '</p>';
		return;
	}
	$sent = 'yes' === get_post_meta( $post->ID, 'notify_sent', true );
	printf(
		'<p><strong>%1$s</strong> %2$s<br><strong>%3$s</strong> %4$s</p>',
		esc_html__( 'Sent to:', 'altura-leads' ),
		esc_html( $to ),
		esc_html__( 'Handed to the mail server:', 'altura-leads' ),
		$sent ? esc_html__( 'yes', 'altura-leads' ) : esc_html__( 'no (this site cannot send email)', 'altura-leads' )
	);
	printf( '<pre style="white-space:pre-wrap;background:#f6f7f7;padding:12px;border:1px solid #dcdcde;margin:0">%s</pre>', esc_html( $body ) );
	echo '<p class="description">' . esc_html__( 'Change the address this goes to in Settings > ALTURA.', 'altura-leads' ) . '</p>';
}

/**
 * Status dropdown.
 *
 * @param WP_Post $post Lead.
 */
function npl_status_box( $post ) {
	wp_nonce_field( 'npl_status', 'npl_status_nonce' );
	$current = get_post_meta( $post->ID, 'status', true ) ?: 'new';
	echo '<label class="screen-reader-text" for="npl-status">' . esc_html__( 'Status', 'altura-leads' ) . '</label><select id="npl-status" name="npl_status" style="width:100%">';
	foreach ( npl_statuses() as $key => $label ) {
		printf( '<option value="%s"%s>%s</option>', esc_attr( $key ), selected( $current, $key, false ), esc_html( $label ) );
	}
	echo '</select>';
}

add_action(
	'save_post_npl_lead',
	function ( $post_id ) {
		if ( ! isset( $_POST['npl_status_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['npl_status_nonce'] ), 'npl_status' ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		$status = isset( $_POST['npl_status'] ) ? sanitize_key( $_POST['npl_status'] ) : 'new';
		if ( array_key_exists( $status, npl_statuses() ) ) {
			update_post_meta( $post_id, 'status', $status );
		}
	}
);

/**
 * Count of new requests next to the menu item.
 */
add_action(
	'admin_menu',
	function () {
		global $menu;
		$new = get_posts(
			array(
				'post_type'   => 'npl_lead',
				'numberposts' => 99,
				'fields'      => 'ids',
				'meta_query'  => array( array( 'key' => 'status', 'value' => 'new' ) ), // phpcs:ignore WordPress.DB.SlowDBQuery
			)
		);
		if ( ! $new ) {
			return;
		}
		foreach ( $menu as $i => $item ) {
			if ( 'edit.php?post_type=npl_lead' === $item[2] ) {
				$menu[ $i ][0] .= ' <span class="awaiting-mod"><span class="count">' . count( $new ) . '</span></span>'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
			}
		}
	},
	99
);
