/**
 * Set up store
 */
import './store'

/**
 * Import blocks
 */

// Teaser blocks
import './blocks/article-teasers'
import './blocks/citation-teasers'
import './blocks/event-teasers'
import './blocks/news-item-teasers'
import './blocks/profile-teasers'
import './blocks/research-topic-teasers'
import './blocks/research-review-teasers'

// Profile blocks
import './blocks/profile-bio'
import './blocks/profile-photo'
import './blocks/profile-research-topics'
import './blocks/profile-title-institution'
import './blocks/profile-vital-link'

// Miscellaneous
import './blocks/citation-library-filters'
import './blocks/cite-this'
import './blocks/homepage-slides'
import './blocks/item-byline'
import './blocks/item-type-label'
import './blocks/profile-directory-filters'
import './blocks/review-version-selector'
import './blocks/social-buttons'
import './blocks/suggested-items'
import './blocks/table-of-contents'
import './blocks/zotero-library-info-help'

/**
 * Shared block styles.
 */
import '../css/blocks.css'

/**
 * Components
 */
import { registerPlugin } from '@wordpress/plugins';

// Register the Zotero Library Info inspector control.
import renderZoteroLibraryInfo from './components/ZoteroLibraryInfo';
registerPlugin(
	'zotero-library-info',
	{
		icon: 'book-alt',
		render: renderZoteroLibraryInfo,
	}
)

// Add Profile Inspector controls.
import ProfileSettingsControls from './components/ProfileSettingsControls'
registerPlugin(
	'ramp-profile-settings-controls',
	{
		icon: 'users',
		render: ProfileSettingsControls
	}
)

// Swap out the selector control for some custom taxonomies.
import ReorderableFlatTermSelector from './components/ReorderableFlatTermSelector'

const selectTaxonomySelector = ( OriginalComponent ) => {
	return ( props ) => {
		if ( 'ramp_assoc_profile' === props.slug || 'ramp_assoc_topic' === props.slug ) {
			return <ReorderableFlatTermSelector {...props} />
		} else {
			return <OriginalComponent {...props} />
		}
	}
}

wp.hooks.addFilter(
	'editor.PostTaxonomyType',
	'ramp/select-taxonomy-selector',
	selectTaxonomySelector
)
