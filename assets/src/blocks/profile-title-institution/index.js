/**
 * Profile Title/Institution block.
 */

import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import edit from './edit';
import save from './save';
import metadata from './block.json';
import { person as icon } from '../../icons'

/**
 * Block definition.
 */
registerBlockType( metadata, {
	icon,
	edit,
	save
} );
