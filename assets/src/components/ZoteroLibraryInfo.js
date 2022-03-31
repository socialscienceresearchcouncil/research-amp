import { Button, TextControl } from '@wordpress/components'
import { dispatch, select } from '@wordpress/data'
import { PluginDocumentSettingPanel } from '@wordpress/edit-post'
import { Component } from '@wordpress/element'
import { addQueryArgs } from '@wordpress/url'
import { __ } from '@wordpress/i18n'
import { useSelect } from '@wordpress/data'
import { useState } from '@wordpress/element'
import { store as coreStore } from '@wordpress/core-data'
import apiFetch from '@wordpress/api-fetch'

export default function ZoteroLibraryInfo( {
	attributes,
	setAttributes
} ) {
	const postType = select( 'core/editor' ).getCurrentPostType();
	if ( 'ramp_zotero_library' !== postType ) {
		return null;
	}

	const { editPost } = dispatch( 'core/editor' )

	const { apiKey, libraryId, libraryInfo, postId } = useSelect( ( select ) => {
		const {
			zotero_api_key,
			zotero_library_id
		} = select( 'core/editor' ).getEditedPostAttribute( 'meta' )

		const postId = select( 'core/editor' ).getCurrentPostId()

		const libraryInfo = select( 'ramp' ).getLibraryInfo( postId )

		return {
			apiKey: zotero_api_key,
			libraryId: zotero_library_id,
			libraryInfo,
			postId
		}
	}, [] )

	const triggerIngest = () => {
		apiFetch( {
			path: addQueryArgs( 'ramp/v1/zotero-library', {
				libraryId: postId
			} ),
			method: 'POST'
		} ).then( ( res ) => {
			console.log( 'I heard back' )
		} )
	}

	const editPostMeta = ( newMeta ) => {
		editPost( { meta: newMeta } )
	}

	let nextIngest
	if ( libraryInfo ) {
		nextIngest = libraryInfo.nextIngest
	}

	return (
		<PluginDocumentSettingPanel
			name="ramp-zotero-library-info"
			title={ __( 'Library Settings', 'ramp' ) }
			>

			<TextControl
				label={ __( 'Library ID', 'ramp' ) }
				value={ libraryId }
				onChange={ (value) => { editPostMeta( { zotero_library_id: value } ) } }
			/>

			<TextControl
				label={ __( 'API Key', 'ramp' ) }
				value={ apiKey }
				onChange={ (value) => { editPostMeta( { zotero_api_key: value } ) } }
			/>

			{nextIngest}

			<Button
				variant="primary"
				onClick={ triggerIngest }
			>Trigger Zotero sync</Button>
		</PluginDocumentSettingPanel>
	);
}
