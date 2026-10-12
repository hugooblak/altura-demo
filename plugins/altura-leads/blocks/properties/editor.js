/** Editor side of the Properties block. The preview is rendered by the same PHP as the live site. */
( function ( wp ) {
	var el = wp.element.createElement;
	var __ = wp.i18n.__;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var PanelBody = wp.components.PanelBody;
	var SelectControl = wp.components.SelectControl;
	var RangeControl = wp.components.RangeControl;
	var Disabled = wp.components.Disabled;
	var ServerSideRender = wp.serverSideRender;

	wp.blocks.registerBlockType( 'altura/properties', {
		edit: function ( props ) {
			var a = props.attributes;
			return el(
				'div',
				useBlockProps(),
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Properties', 'altura-leads' ) },
						el( SelectControl, {
							label: __( 'Layout', 'altura-leads' ),
							value: a.display,
							options: [
								{ label: __( 'Grid: photo, name and place', 'altura-leads' ), value: 'grid' },
								{ label: __( 'List: adds price and key facts', 'altura-leads' ), value: 'list' },
							],
							onChange: function ( v ) { props.setAttributes( { display: v } ); },
						} ),
						el( RangeControl, {
							label: __( 'How many to show', 'altura-leads' ),
							min: 1,
							max: 24,
							value: a.count,
							onChange: function ( v ) { props.setAttributes( { count: v } ); },
						} )
					)
				),
				el( Disabled, null, el( ServerSideRender, { block: 'altura/properties', attributes: a } ) )
			);
		},
		save: function () { return null; },
	} );
} )( window.wp );
