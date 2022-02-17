/**
 * Citation Teasers block.
 */

import { registerBlockType } from '@wordpress/blocks'

/**
 * Internal dependencies
 */
import edit from './edit'
import metadata from './block.json'

/**
 * Block definition.
 */
registerBlockType( metadata, {
	edit,
	metadata,
	save: () => { return null },
} )
