/** Editor side of the Property facts block. Preview comes from the same PHP as the live page. */
( function ( wp ) {
	var el = wp.element.createElement;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var Disabled = wp.components.Disabled;
	var ServerSideRender = wp.serverSideRender;

	wp.blocks.registerBlockType( 'altura/property-facts', {
		edit: function ( props ) {
			return el(
				'div',
				useBlockProps(),
				el( Disabled, null, el( ServerSideRender, { block: 'altura/property-facts', attributes: props.attributes, urlQueryArgs: { post_id: props.context && props.context.postId } } ) )
			);
		},
		save: function () { return null; },
	} );
} )( window.wp );
