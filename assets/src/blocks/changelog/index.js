/**
 * Review Version Selector block
 */

import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import edit from './edit';
import save from './save';
import metadata from './block.json';
import { changelog as icon } from '../../icons'

/**
 * Block definition.
 */
registerBlockType( metadata, {
	icon,
	edit,
	save
} );
