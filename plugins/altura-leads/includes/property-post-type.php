<?php
/**
 * Properties: the one thing the agency edits most often.
 *
 * A property is content, not design. The title, price and facts live in one place and every
 * screen reads them from there: the hero slider, the home page grid, the Properties page, the
 * property's own page, the enquiry form's dropdown (by name and place).
 *
 * Public on purpose (unlike the enquiries): each property has its own address and appears in the
 * sitemap. The listing page is an ordinary page that holds the Properties block.
 *
 * @package ALTURAProperties
 */

defined( 'ABSPATH' ) || exit;

/**
 * The fields on a property, in the order they are shown.
 *
 * Each: label, type (text|number|select), and a hint for the agent.
 *
 * @return array
 */
function al_property_fields() {
	return array(
		'place'    => array( __( 'Place', 'altura-leads' ), 'text', __( 'Town and country, for example "Corfu, Greece". Shown under the name everywhere.', 'altura-leads' ) ),
		'price'    => array( __( 'Asking price in euros', 'altura-leads' ), 'number', __( 'Numbers only. Leave empty or 0 to show "Price on request".', 'altura-leads' ) ),
		'status'   => array( __( 'Status', 'altura-leads' ), 'select', __( 'A sold property stays on the site but its enquiry button is replaced by a note and an "Ask about similar" button.', 'altura-leads' ) ),
		'beds'     => array( __( 'Bedrooms', 'altura-leads' ), 'number', '' ),
		'baths'    => array( __( 'Bathrooms', 'altura-leads' ), 'number', '' ),
		'size'     => array( __( 'Living area in m²', 'altura-leads' ), 'number', '' ),
		'plot'     => array( __( 'Plot in m²', 'altura-leads' ), 'number', __( 'Optional.', 'altura-leads' ) ),
		'ref'      => array( __( 'Reference', 'altura-leads' ), 'text', __( 'Shown on the property page and in the enquiry email, for example AL-0101.', 'altura-leads' ) ),
		'focus'    => array( __( 'Photo focus, % from the left', 'altura-leads' ), 'number', __( 'The photo is cropped in the slider. 0 is the left edge, 100 the right. Move it until the house is in view.', 'altura-leads' ) ),
	);
}

/**
 * Choices for the status field.
 *
 * @return array
 */
function al_property_statuses() {
	return array(
		'available'   => __( 'Available', 'altura-leads' ),
		'under-offer' => __( 'Under offer', 'altura-leads' ),
		'sold'        => __( 'Sold', 'altura-leads' ),
	);
}

add_action(
	'init',
	function () {
		register_post_type(
			'al_property',
			array(
				'labels'        => array(
					'name'          => __( 'Properties', 'altura-leads' ),
					'singular_name' => __( 'Property', 'altura-leads' ),
					'add_new_item'  => __( 'Add a property', 'altura-leads' ),
					'edit_item'     => __( 'Edit property', 'altura-leads' ),
					'all_items'     => __( 'All properties', 'altura-leads' ),
					'not_found'     => __( 'No properties yet. Add one and it appears on the site straight away.', 'altura-leads' ),
				),
				'public'        => true,
				'has_archive'   => false, // The Properties page is an ordinary page holding the Properties block.
				'rewrite'       => array( 'slug' => 'property', 'with_front' => false ),
				'show_in_rest'  => true,  // Needed for the block editor.
				'menu_icon'     => 'dashicons-admin-home',
				'menu_position' => 4,
				'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
			)
		);
	}
);

/**
 * Published properties, in the order set by "Order" on each one.
 *
 * @return WP_Post[]
 */
function al_properties() {
	static $cache = null;
	if ( null === $cache ) {
		$cache = get_posts(
			array(
				'post_type'   => 'al_property',
				'numberposts' => -1,
				'post_status' => 'publish',
				'orderby'     => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
			)
		);
	}
	return $cache;
}

/**
 * Price as text. "Price on request" when none is set.
 *
 * @param int $post_id Property.
 * @return string
 */
function al_price( $post_id ) {
	$price = (int) get_post_meta( $post_id, 'price', true );
	if ( $price <= 0 ) {
		return __( 'Price on request', 'altura-leads' );
	}
	/* translators: %s: amount in euros. */
	return sprintf( __( '€%s', 'altura-leads' ), number_format_i18n( $price ) );
}

/**
 * The key facts as a short list of label => text, skipping anything that is empty.
 *
 * @param int $post_id Property.
 * @return array
 */
function al_facts( $post_id ) {
	$facts = array();
	$beds  = (int) get_post_meta( $post_id, 'beds', true );
	$baths = (int) get_post_meta( $post_id, 'baths', true );
	$size  = (int) get_post_meta( $post_id, 'size', true );
	$plot  = (int) get_post_meta( $post_id, 'plot', true );
	if ( $beds ) {
		$facts[ __( 'Bedrooms', 'altura-leads' ) ] = (string) $beds;
	}
	if ( $baths ) {
		$facts[ __( 'Bathrooms', 'altura-leads' ) ] = (string) $baths;
	}
	if ( $size ) {
		/* translators: %s: area in square metres. */
		$facts[ __( 'Living area', 'altura-leads' ) ] = sprintf( __( '%s m²', 'altura-leads' ), number_format_i18n( $size ) );
	}
	if ( $plot ) {
		/* translators: %s: area in square metres. */
		$facts[ __( 'Plot', 'altura-leads' ) ] = sprintf( __( '%s m²', 'altura-leads' ), number_format_i18n( $plot ) );
	}
	return $facts;
}

/**
 * Where the "Enquire" buttons point, with the property pre-selected in the form.
 *
 * @param string $slug Optional property slug.
 * @return string
 */
function al_enquire_url( $slug = '' ) {
	$url = home_url( '/enquire/' );
	if ( $slug ) {
		$url = add_query_arg( 'property', rawurlencode( $slug ), $url );
	}
	return $url . '#enquire';
}

/* --------------------------------------------------------------------------
 * Admin: one box of plain fields per property.
 * ----------------------------------------------------------------------- */

add_action(
	'add_meta_boxes_al_property',
	function () {
		add_meta_box( 'al_property_fields', __( 'Property details', 'altura-leads' ), 'al_fields_box', 'al_property', 'normal', 'high' );
	}
);

/**
 * Draws the field box.
 *
 * @param WP_Post $post Property being edited.
 */
function al_fields_box( $post ) {
	wp_nonce_field( 'al_fields', 'al_fields_nonce' );
	echo '<table class="form-table" role="presentation"><tbody>';
	foreach ( al_property_fields() as $key => $field ) {
		list( $label, $type, $hint ) = $field;
		$value = get_post_meta( $post->ID, $key, true );
		$id    = 'al-' . $key;
		printf( '<tr><th scope="row"><label for="%s">%s</label></th><td>', esc_attr( $id ), esc_html( $label ) );
		if ( 'number' === $type ) {
			printf( '<input type="number" min="0" step="1" id="%s" name="al_%s" value="%s" class="small-text">', esc_attr( $id ), esc_attr( $key ), esc_attr( $value ) );
		} elseif ( 'select' === $type ) {
			printf( '<select id="%s" name="al_%s">', esc_attr( $id ), esc_attr( $key ) );
			foreach ( al_property_statuses() as $option_value => $option_label ) {
				printf( '<option value="%s"%s>%s</option>', esc_attr( $option_value ), selected( $value ?: 'available', $option_value, false ), esc_html( $option_label ) );
			}
			echo '</select>';
		} else {
			printf( '<input type="text" id="%s" name="al_%s" value="%s" class="regular-text">', esc_attr( $id ), esc_attr( $key ), esc_attr( $value ) );
		}
		if ( $hint ) {
			printf( '<p class="description">%s</p>', esc_html( $hint ) );
		}
		echo '</td></tr>';
	}
	echo '</tbody></table>';
	printf(
		'<p class="description">%s</p>',
		esc_html__( 'The photo is the Featured image. The short line under the name comes from the Excerpt box and the longer text is the main editor above. Order the properties with "Order" in Page Attributes: the first six fill the slider.', 'altura-leads' )
	);
}

add_action(
	'save_post_al_property',
	function ( $post_id ) {
		if ( ! isset( $_POST['al_fields_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['al_fields_nonce'] ), 'al_fields' ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}
		foreach ( al_property_fields() as $key => $field ) {
			if ( ! isset( $_POST[ 'al_' . $key ] ) ) {
				continue;
			}
			$raw = wp_unslash( $_POST[ 'al_' . $key ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			if ( 'number' === $field[1] ) {
				$value = (string) min( absint( $raw ), 'focus' === $key ? 100 : 999999999 );
			} elseif ( 'select' === $field[1] ) {
				$value = array_key_exists( $raw, al_property_statuses() ) ? $raw : 'available';
			} else {
				$value = sanitize_text_field( $raw );
			}
			update_post_meta( $post_id, $key, $value );
		}
	}
);

/* --------------------------------------------------------------------------
 * Admin: useful columns instead of just a title and a date.
 * ----------------------------------------------------------------------- */

add_filter(
	'manage_al_property_posts_columns',
	function () {
		return array(
			'cb'        => '<input type="checkbox" />',
			'title'     => __( 'Property', 'altura-leads' ),
			'al_place'  => __( 'Place', 'altura-leads' ),
			'al_price'  => __( 'Price', 'altura-leads' ),
			'al_status' => __( 'Status', 'altura-leads' ),
			'al_order'  => __( 'Order', 'altura-leads' ),
		);
	}
);

add_action(
	'manage_al_property_posts_custom_column',
	function ( $column, $post_id ) {
		switch ( $column ) {
			case 'al_place':
				echo esc_html( get_post_meta( $post_id, 'place', true ) );
				break;
			case 'al_price':
				echo esc_html( al_price( $post_id ) );
				break;
			case 'al_status':
				$statuses = al_property_statuses();
				$value    = get_post_meta( $post_id, 'status', true ) ?: 'available';
				echo esc_html( $statuses[ $value ] ?? $value );
				break;
			case 'al_order':
				echo (int) get_post_field( 'menu_order', $post_id );
				break;
		}
	},
	10,
	2
);

/**
 * Photo for a property: a sized <img> tag from the Featured image, or an empty string.
 *
 * @param WP_Post|int $property Property.
 * @param string      $size     Registered image size.
 * @param array       $attr     Extra attributes.
 * @return string
 */
function al_property_img( $property, $size = 'large', $attr = array() ) {
	$id = get_post_thumbnail_id( $property );
	return $id ? wp_get_attachment_image( $id, $size, false, $attr ) : '';
}
