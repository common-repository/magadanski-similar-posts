( function( blocks, element, data ) {
    var el = element.createElement,
        registerBlockType = blocks.registerBlockType,
        withSelect = data.withSelect;
 
    registerBlockType( 'magadanski/similar-posts', {
        title: 'Similar Posts',
        icon: 'randomize',
        category: 'widgets',
        edit: function( props ) {
            return (
                el( ServerSideRender, {
                    block: 'magadanski/similar-posts',
                    attributes: props.attributes,
                } )
            );
        },
    } );
}(
    window.wp.blocks,
    window.wp.element,
    window.wp.data,
) );