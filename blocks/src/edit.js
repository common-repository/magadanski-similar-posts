const { __ } = wp.i18n;
const { filter, map, find } = lodash
const { withSelect } = wp.data;
const { Component } = wp.element;
const { InspectorControls } = wp.blockEditor;
const { PanelBody, RangeControl, SelectControl, CheckboxControl } = wp.components;
const ServerSideRender = wp.serverSideRender;

class SimilarPosts extends Component {
	constructor() {
		super( ...arguments );
		
		this.state = {
			editing: ! this.props.attributes.taxonomy,
		};
		
		this.setPostType = this.setPostType.bind( this );
		this.setTaxonomy = this.setTaxonomy.bind( this );
		this.setLimit = this.setLimit.bind( this );
		
		this.setPostType(this.props.currentPostType);
	}
	
	getTaxonomyOptions() {
		const postTypeTaxonomies = filter( this.props.taxonomies, ( t ) => {
			return t.types.includes( this.props.currentPostType );
		} );
		
		const taxonomyOptions = map( postTypeTaxonomies, ( t ) => {
			return { value: t.slug, label: t.name };
		} );
		
		return taxonomyOptions;
	}
	
	setPostType( post_type ) {
		this.props.setAttributes( { post_type } );
	}
	
	setTaxonomy( taxonomy ) {
		this.props.setAttributes( { taxonomy } );
	}
	
	setLimit( limit ) {
		this.props.setAttributes( { limit } );
	}
	
	render() {
		const { attributes } = this.props;
		const { taxonomy, limit } = attributes;
		const taxonomyOptions = this.getTaxonomyOptions();
		
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Similarity Settings', 'magadanski-similar-posts' ) }>
						<SelectControl
							label={ __( 'Taxonomy', 'magadanski-similar-posts' ) }
							options={ taxonomyOptions }
							value={ taxonomy }
							onChange={ this.setTaxonomy }
						/>
						
						<RangeControl
							label={ __( 'Maximum number of entries to show', 'magadanski-similar-posts' ) }
							value={ limit }
							onChange={ this.setLimit }
							min={ 1 }
							max={ 10 }
							required
						/>
					</PanelBody>
				</InspectorControls>
				
				<ServerSideRender
					block="magadanski/similar-posts"
					attributes={ attributes }
				/>
			</>
		);
	}
}

export default withSelect( ( select ) => {
	return {
		taxonomies: select( 'core' ).getTaxonomies(),
		currentPostType: select( 'core/editor' ).getCurrentPost().type
	}
} )( SimilarPosts );