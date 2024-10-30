const { registerBlockType } = wp.blocks;

import edit from './edit.js';

registerBlockType( 'magadanski/similar-posts', {
	title: 'Similar Posts',
	icon: 'randomize',
	category: 'widgets',
	supports: {
		html: false
	},
	
	edit
} );