<?php
/**
 * "Export CSV" button on the Enquiries screen. Opens in Excel or Google Sheets.
 *
 * @package ALTURAProperties
 */

defined( 'ABSPATH' ) || exit;

add_filter(
	'views_edit-npl_lead',
	function ( $views ) {
		if ( ! current_user_can( 'export_npl_leads' ) ) {
			return $views;
		}
		$url = wp_nonce_url( admin_url( 'admin-post.php?action=npl_export' ), 'npl_export' );
		$views['npl_export'] = '<a href="' . esc_url( $url ) . '" class="button" style="margin-left:6px">' . esc_html__( 'Export CSV', 'altura-leads' ) . '</a>';
		return $views;
	}
);

add_action(
	'admin_post_npl_export',
	function () {
		if ( ! current_user_can( 'export_npl_leads' ) ) {
			wp_die( esc_html__( 'You are not allowed to export enquiries.', 'altura-leads' ), 403 );
		}
		check_admin_referer( 'npl_export' );

		$leads = get_posts(
			array(
				'post_type'   => 'npl_lead',
				'numberposts' => -1,
				'post_status' => 'publish',
			)
		);

		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=enquiries-' . gmdate( 'Y-m-d' ) . '.csv' );

		$out     = fopen( 'php://output', 'w' );
		$columns = array( 'received', 'status', 'name', 'email', 'telephone', 'property', 'budget', 'message' );
		fputcsv( $out, $columns );
		foreach ( $leads as $lead ) {
			$row = array(
				$lead->post_date,
				get_post_meta( $lead->ID, 'status', true ),
				get_post_meta( $lead->ID, 'name', true ),
				get_post_meta( $lead->ID, 'email', true ),
				get_post_meta( $lead->ID, 'phone', true ),
				npl_label( 'property', get_post_meta( $lead->ID, 'property', true ) ),
				npl_label( 'budget', get_post_meta( $lead->ID, 'budget', true ) ),
				get_post_meta( $lead->ID, 'message', true ),
			);
			// Stop spreadsheet formula injection: a cell starting with = + - @ is treated as text.
			$row = array_map(
				function ( $cell ) {
					// A plain phone number such as +44 20 7946 0000 is safe, so leave it alone.
					if ( preg_match( '/^\+?[\d ()\-]+$/', (string) $cell ) ) {
						return $cell;
					}
					return preg_match( '/^[=+\-@]/', (string) $cell ) ? "'" . $cell : $cell;
				},
				$row
			);
			fputcsv( $out, $row );
		}
		fclose( $out ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		exit;
	}
);
