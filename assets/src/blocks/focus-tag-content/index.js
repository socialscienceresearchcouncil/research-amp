/**
 * Focus Tag Content block.
 */

import { registerBlockType } from '@wordpress/blocks';
import edit from './edit';
import metadata from './block.json';

/**
 * Block definition.
 */
registerBlockType( metadata, {
	edit,
	save: () => {
		return null;
	},
} );
