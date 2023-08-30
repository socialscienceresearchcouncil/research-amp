import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import edit from './edit';
import metadata from './block.json';
import { library as icon } from '../../icons';

registerBlockType( metadata, {
	icon,
	edit,
	save: () => {
		return null;
	},
} );
