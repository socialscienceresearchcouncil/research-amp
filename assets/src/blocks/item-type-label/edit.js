import './editor.scss'

import { __, sprintf } from '@wordpress/i18n'

import {
	useBlockProps
} from '@wordpress/block-editor'

import { store as coreStore } from '@wordpress/core-data'
import { store as postStore } from '@wordpress/editor'

import { useSelect } from '@wordpress/data'

import PublicationDateToggle from '../../components/PublicationDateToggle'

export default function edit( {
	context: { postType, postId, templateSlug },
	attributes,
	setAttributes
} ) {
	const {
		showPublicationDate
	} = attributes

	const blockProps = useBlockProps()

	const { articleType } = useSelect(
		( select ) => {
			const { getEditedEntityRecord, getEntityRecords } = select( coreStore )
			const { getEditedPostAttribute } = select( postStore )

			const _publicationDate = getEditedEntityRecord(
				'postType',
				postType,
				postId
			)?.formatted_date;

			const articleTypeIds = getEditedPostAttribute( 'article-types' )

			const articleTypeTerms = typeof articleTypeIds !== 'undefined' && articleTypeIds.length
				? getEntityRecords( 'taxonomy', 'ramp_article_type', { include: articleTypeIds, context: 'view' } )
				: []

			// For now, we simply take the first one
			const articleTypeLabel = articleTypeTerms?.length ? articleTypeTerms[0].name : ''

			return {
				articleType: articleTypeLabel
			};
		},
		[ postType, postId ]
	);

	const getArticleTypeFromTemplateSlug = ( templateSlug ) => {
		if ( templateSlug ) {
			switch ( templateSlug ) {
				case 'single-ramp_review_version' :
					return __( 'Research Review', 'ramp' )

				case 'single-ramp_article' :
					return __( 'Article Type', 'ramp' )
			}
		}

		return ''
	}

	const articleTypeFromTemplate = getArticleTypeFromTemplateSlug( templateSlug )

	const articleTypeLabel = articleType.length > 0 ? articleType : articleTypeFromTemplate

	return (
		<>
			<div { ...blockProps }> { articleTypeLabel } </div>
		</>
	)
}
