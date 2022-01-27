/**
 * Set up store
 */
import './store'

/**
 * Import blocks
 */

// Teaser blocks
import './blocks/article-teasers'
import './blocks/article-teasers-with-featured-article'
import './blocks/citation-teasers'
import './blocks/news-item-teasers'
import './blocks/research-topic-teasers'
import './blocks/research-review-teasers'

// Profile blocks
import './blocks/profile-research-topics'
import './blocks/profile-vital-link'

// Miscellaneous
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
