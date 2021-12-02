/**
 * Set up store
 */
import './store'

/**
 * Import blocks
 */
import './blocks/research-topics'
import './blocks/zotero-library-info-help'

/**
 * Components
 */
import { registerPlugin } from '@wordpress/plugins';

import renderZoteroLibraryInfo from './components/ZoteroLibraryInfo';
registerPlugin(
	'zotero-library-info',
	{
		icon: 'book-alt',
		render: renderZoteroLibraryInfo,
	}
);
