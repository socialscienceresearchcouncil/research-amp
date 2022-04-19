/**
 * Item Byline block
 */

import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import edit from './edit';
import metadata from './block.json';

/**
 * Block definition
 */
registerBlockType( metadata, {
	edit,
	save: () => { return null }
} );