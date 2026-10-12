/**
 * Editor side of the hero block. No build step: plain JavaScript using WordPress's globals.
 * The preview is rendered by the same PHP as the live site.
 */
( function ( wp ) {
	var el = wp.element.createElement;
	var __ = wp.i18n.__;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var Disabled = wp.components.Disabled;
	var ServerSideRender = wp.serverSideRender;

	function field( props, key, label ) {
		return el( TextControl, {
			label: label,
			value: props.attributes[ key ],
			onChange: function ( v ) { var o = {}; o[ key ] = v; props.setAttributes( o ); },
		} );
	}

	wp.blocks.registerBlockType( 'altura/hero', {
		edit: function ( props ) {
			return el(
				'div',
				useBlockProps(),
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Hero text', 'altura-leads' ) },
						field( props, 'headlineA', __( 'Headline, first line', 'altura-leads' ) ),
						field( props, 'headlineB', __( 'Headline, second line (italic)', 'altura-leads' ) ),
						field( props, 'subA', __( 'Small text, first line', 'altura-leads' ) ),
						field( props, 'subB', __( 'Small text, second line', 'altura-leads' ) ),
						el( 'p', { style: { color: '#555' } }, __( 'The photos, names and places come from the Properties screen. The first six properties with a Featured image fill the slider.', 'altura-leads' ) )
					)
				),
				el( Disabled, null, el( ServerSideRender, { block: 'altura/hero', attributes: props.attributes } ) )
			);
		},
		save: function () { return null; },
	} );
} )( window.wp );
