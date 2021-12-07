/**
 * Research Topics block.
 */

import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import edit from './edit';
import metadata from './block.json';

/**
 * Block definition.
 */
registerBlockType( metadata, {
	/**
	 * @see ./edit.js
	 */
	edit,

	/**
	 * Rendered in PHP.
	 */
	save: () => { return null },
} );
