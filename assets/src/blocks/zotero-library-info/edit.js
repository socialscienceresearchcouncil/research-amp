import { __, sprintf } from '@wordpress/i18n'

import {
	InspectorControls,
	useBlockProps
} from '@wordpress/block-editor'

import {
	Button,
	Panel,
	PanelBody,
	TextControl
} from '@wordpress/components'

import { dispatch, select, useSelect, useDispatch } from '@wordpress/data'
import { addQueryArgs } from '@wordpress/url'

import apiFetch from '@wordpress/api-fetch'

/**
 * Editor styles.
 */
import './editor.scss'

/**
 * Edit function.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function edit( {
	attributes,
	setAttributes
} ) {
	const { editPost } = dispatch( 'core/editor' )

	const { apiKey, libraryId, libraryInfo, postId, postTitle } = useSelect( ( select ) => {
		const {
			zotero_api_key,
			zotero_library_id
		} = select( 'core/editor' ).getEditedPostAttribute( 'meta' )

		const postTitle = select( 'core/editor' ).getEditedPostAttribute( 'title' )

		const postId = select( 'core/editor' ).getCurrentPostId()

		const libraryInfo = select( 'research-amp' ).getLibraryInfo( postId )

		return {
			apiKey: zotero_api_key,
			libraryId: zotero_library_id,
			libraryInfo,
			postId,
			postTitle
		}
	}, [] )

	const triggerIngest = () => {
		apiFetch( {
			path: addQueryArgs( 'research-amp/v1/zotero-library', {
				libraryId: postId,
				action: 'sync'
			} ),
			method: 'POST'
		} ).then( ( res ) => {
			console.log( 'I heard back' )
		} )
	}

	const editPostMeta = ( newMeta ) => {
		editPost( { meta: newMeta } )
	}

	const editPostTitle = ( title ) => {
		editPost( { title } )
	}

	const lastIngest = libraryInfo?.lastIngest
	const lastIngestRelative = libraryInfo?.lastIngestRelative
	const nextIngest = libraryInfo?.nextIngest
	const nextIngestRelative = libraryInfo?.nextIngestRelative

	const blockProps = () => {
		const classNames = [ 'ramp-zotero-library-info' ]

		return useBlockProps( {
			className: classNames
		} )
	}

	return (
		<>
			<div { ...blockProps() }>
				<fieldset>
					<legend>{ __( 'Library Settings', 'research-amp' ) }</legend>

					<TextControl
						label={ __( 'Library Name', 'research-amp' ) }
						value={ postTitle }
						onChange={ (postTitle) => { editPostTitle( postTitle ) } }
						help={ __( 'Displayed on individual Citation pages, to indicate the source library for that item.', 'research-amp' ) }
					/>

					<TextControl
						label={ __( 'Library ID', 'research-amp' ) }
						value={ libraryId }
						onChange={ (value) => { editPostMeta( { zotero_library_id: value } ) } }
					/>

					<TextControl
						label={ __( 'API Key', 'research-amp' ) }
						type="password"
						value={ apiKey }
						onChange={ (value) => { editPostMeta( { zotero_api_key: value } ) } }
					/>
				</fieldset>

				<fieldset>
					<legend>{ __( 'Sync', 'research-amp' ) }</legend>

					<p>
						{ sprintf(
							/* translators: 1. Timestamp for last sync; 2. Relative time since last sync */
							__( 'Last library sync: %1$s (%2$s)', 'research-amp' ),
							lastIngest,
							lastIngestRelative
						) }
					</p>

					<p>
						{ sprintf(
							/* translators: 1. Timestamp for next sync; 2. Relative time until next sync */
							__( 'Next library sync: %1$s (%2$s)', 'research-amp' ),
							nextIngest,
							nextIngestRelative
						) }
					</p>

					<p>{ __( 'You may trigger an immediate sync by clicking the button below. (New sync timestamps may not appear for a few minutes.)', 'research-amp' ) }</p>

					<Button
						variant="primary"
						onClick={ triggerIngest }
					>{ __( 'Trigger Zotero sync', 'research-amp' ) }</Button>

				</fieldset>
			</div>
		</>
	);
}
