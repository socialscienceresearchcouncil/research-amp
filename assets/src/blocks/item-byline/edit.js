import './editor.scss'

import { __, sprintf } from '@wordpress/i18n'

import {
	InspectorControls,
	useBlockProps
} from '@wordpress/block-editor'

import { store as coreStore } from '@wordpress/core-data'
import { store as postStore } from '@wordpress/editor'

import { useSelect } from '@wordpress/data'

import { PanelBody } from '@wordpress/components'

import PublicationDateToggle from '../../components/PublicationDateToggle'

export default function edit( {
	context: { postType, postId },
	attributes,
	setAttributes
} ) {
	const {
		showPublicationDate
	} = attributes

	const blockProps = useBlockProps()

	const { authorName, postDate } = useSelect(
		( select ) => {
			const { getEditedEntityRecord, getEntityRecords } = select( coreStore )
			const { getEditedPostAttribute } = select( postStore )

			const _publicationDate = getEditedEntityRecord(
				'postType',
				postType,
				postId
			)?.formatted_date;

			const assocProfileIds = getEditedPostAttribute( 'associated-profiles' )

			const assocProfileTerms = typeof assocProfileIds !== 'undefined' && assocProfileIds.length
				? getEntityRecords( 'taxonomy', 'ramp_assoc_profile', { include: assocProfileIds, context: 'view' } )
				: []

			// Iterate over profile IDs to maintain sort order.
			const authorNames = null !== assocProfileTerms && assocProfileTerms.length
				? assocProfileIds.map( (termId) => {
						const profileTerm = assocProfileTerms.find( (term) => termId === term.id )
						return profileTerm?.name || ''
					} )
				: []

			return {
				authorName: authorNames.length ? authorNames.join( ', ' ) : null,
				postDate: _publicationDate ?? null
			};
		},
		[ postType, postId ]
	);

	const displayName = authorName ?? __( 'Author Name', 'research-amp' )
	const publicationDate = postDate ?? __( 'Publication Date', 'research-amp' )

	const byline = showPublicationDate
		? sprintf( __( 'By %1$s on %2$s', 'research-amp' ),
				displayName,
				publicationDate
			)
		: sprintf(
				__( 'By %1$s', 'research-amp' ),
				displayName
			)

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Link settings' ) }>
					<PublicationDateToggle
						onChangeCallback={ ( showPublicationDate ) => setAttributes( { showPublicationDate } ) }
						showPublicationDate={ showPublicationDate }
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }> { byline } </div>
		</>
	)
}
