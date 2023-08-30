import './editor.scss';

import { __, sprintf } from '@wordpress/i18n';

import { useBlockProps } from '@wordpress/block-editor';

import { store as coreStore } from '@wordpress/core-data';
import { store as postStore } from '@wordpress/editor';

import { useSelect } from '@wordpress/data';

import PublicationDateToggle from '../../components/PublicationDateToggle';

export default function edit( {
	context: { postType, postId, templateSlug },
	attributes,
	setAttributes,
} ) {
	const { showPublicationDate } = attributes;

	const blockProps = useBlockProps();

	const { articleType, postTypeLabel } = useSelect(
		( select ) => {
			const { getEditedEntityRecord, getEntityRecords } =
				select( coreStore );
			const { getEditedPostAttribute, getPostTypeLabel } =
				select( postStore );

			const _publicationDate = getEditedEntityRecord(
				'postType',
				postType,
				postId
			)?.formatted_date;

			const articleTypeIds = getEditedPostAttribute( 'article-types' );

			const articleTypeTerms =
				typeof articleTypeIds !== 'undefined' && articleTypeIds.length
					? getEntityRecords( 'taxonomy', 'ramp_article_type', {
							include: articleTypeIds,
							context: 'view',
					  } )
					: [];

			// For now, we simply take the first one
			const articleTypeLabel = articleTypeTerms?.length
				? articleTypeTerms[ 0 ].name
				: '';

			return {
				articleType: articleTypeLabel,
				postTypeLabel: getPostTypeLabel(),
			};
		},
		[ postType, postId ]
	);

	const getArticleTypeFromTemplateSlug = ( templateSlug ) => {
		if ( templateSlug ) {
			switch ( templateSlug ) {
				case 'single-ramp_article':
					return __( 'Article Type', 'research-amp' );

				case 'single':
					return __( 'News Item', 'research-amp' );
			}
		}

		return '';
	};

	const articleTypeFromTemplate =
		getArticleTypeFromTemplateSlug( templateSlug );

	const itemTypeFromTemplate =
		articleTypeFromTemplate.length > 0
			? articleTypeFromTemplate
			: postTypeLabel;

	const articleTypeLabel =
		articleType.length > 0 ? articleType : itemTypeFromTemplate;

	return (
		<>
			<div { ...blockProps }> { articleTypeLabel } </div>
		</>
	);
}
