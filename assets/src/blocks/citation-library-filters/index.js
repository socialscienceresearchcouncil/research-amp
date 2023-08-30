/**
 * Citation Teasers block.
 */

import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import edit from './edit';
import metadata from './block.json';
import { filter as icon } from '../../icons';

/**
 * Block definition.
 */
registerBlockType( metadata, {
	icon,
	edit,
	metadata,
	save: () => {
		return null;
	},
} );
