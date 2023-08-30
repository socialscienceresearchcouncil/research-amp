/**
 * Profile Vital Link block.
 */

import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import edit from './edit';
import save from './save';
import variations from './variations';
import metadata from './block.json';

/**
 * Block definition.
 */
registerBlockType( metadata, {
	edit,
	save,
	variations,
} );
