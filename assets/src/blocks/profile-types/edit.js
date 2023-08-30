import './editor.scss';

import { __, sprintf } from '@wordpress/i18n';

import { useBlockProps } from '@wordpress/block-editor';

import { store as coreStore } from '@wordpress/core-data';
import { store as postStore } from '@wordpress/editor';

import { useSelect } from '@wordpress/data';

export default function edit( {
	context: { postType, postId, templateSlug },
	attributes,
	setAttributes,
} ) {
	const blockProps = useBlockProps();

	const { profileTypes } = useSelect(
		( select ) => {
			const { getEntityRecords } = select( coreStore );
			const { getEditedPostAttribute } = select( postStore );

			// Get the ramp_profile_type taxonomy terms for this post.
			const profileTypeIds =
				getEditedPostAttribute( 'ramp_profile_type' );
			const profileTypeTerms =
				typeof profileTypeIds !== 'undefined' && profileTypeIds.length
					? getEntityRecords( 'taxonomy', 'ramp_profile_type', {
							include: profileTypeIds,
							context: 'view',
					  } )
					: [];

			const profileTypes = profileTypeTerms
				? profileTypeTerms.map( ( term ) => term.name )
				: [];

			return {
				profileTypes,
			};
		},
		[ postId ]
	);

	const profileTypeTags = profileTypes.map( ( profileType ) => {
		return (
			<span className="tag-bubble profile-type-label">
				{ profileType }
			</span>
		);
	} );

	return (
		<>
			<div { ...blockProps }>{ profileTypeTags }</div>
		</>
	);
}
