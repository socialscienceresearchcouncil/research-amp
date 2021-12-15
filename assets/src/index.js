/**
 * Set up store
 */
import './store'

/**
 * Import blocks
 */
import './blocks/article-teasers-with-featured-article'
import './blocks/research-topic-teasers'
import './blocks/research-review-teasers'
import './blocks/zotero-library-info-help'

/**
 * Shared block styles.
 */
import '../css/blocks.css'

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
