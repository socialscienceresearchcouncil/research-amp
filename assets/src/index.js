/**
 * Set up store
 */
import './store';

/**
 * Import blocks
 */

// Teaser blocks
import './blocks/article-teasers';
import './blocks/citation-teasers';
import './blocks/event-teasers';
import './blocks/news-item-teasers';
import './blocks/profile-teasers';
import './blocks/research-topic-teasers';
import './blocks/research-review-teasers';

// Profile blocks
import './blocks/profile-bio';
import './blocks/profile-photo';
import './blocks/profile-title-institution';
import './blocks/profile-vital-link';

// Miscellaneous
import './blocks/changelog';
import './blocks/changelog-entry';
import './blocks/citation-info';
import './blocks/citation-links';
import './blocks/citation-library-count';
import './blocks/citation-library-filters';
import './blocks/cite-this';
import './blocks/focus-tag-content';
import './blocks/homepage-slides';
import './blocks/item-byline';
import './blocks/item-research-topics';
import './blocks/item-type-label';
import './blocks/nav-search';
import './blocks/profile-directory-filters';
import './blocks/profile-types';
import './blocks/search-form';
import './blocks/search-load-more';
import './blocks/search-result-teaser';
import './blocks/search-results-count';
import './blocks/social-buttons';
import './blocks/suggested-items';
import './blocks/table-of-contents';
import './blocks/the-events-calendar';
import './blocks/zotero-library-info';

/**
 * Shared block styles.
 */
import '../css/blocks.css';

/**
 * Components
 */
import { registerPlugin } from '@wordpress/plugins';

// Add Profile Inspector controls.
import ProfileSettingsControls from './components/ProfileSettingsControls';
registerPlugin( 'ramp-profile-settings-controls', {
	icon: 'users',
	render: ProfileSettingsControls,
} );

// Add Citation Inspector controls.
import CitationSettingsControls from './components/CitationSettingsControls';
registerPlugin( 'ramp-citation-settings-controls', {
	icon: 'users',
	render: CitationSettingsControls,
} );

// Swap out the selector control for some custom taxonomies.
import ReorderableFlatTermSelector from './components/ReorderableFlatTermSelector';

const selectTaxonomySelector = ( OriginalComponent ) => {
	return ( props ) => {
		if (
			'ramp_assoc_profile' === props.slug ||
			'ramp_assoc_topic' === props.slug
		) {
			return <ReorderableFlatTermSelector { ...props } />;
		}
		return <OriginalComponent { ...props } />;
	};
};

wp.hooks.addFilter(
	'editor.PostTaxonomyType',
	'research-amp/select-taxonomy-selector',
	selectTaxonomySelector
);
