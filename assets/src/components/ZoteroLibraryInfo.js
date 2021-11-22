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
	if ( 'ssrc_zotero_library' !== postType ) {
		return null;
	}

	const { apiKey, groupId, groupUrl, libraryInfo, postId } = useSelect( ( select ) => {
		const {
			zotero_api_key,
			zotero_group_id,
			zotero_group_url
		} = select( 'core/editor' ).getEditedPostAttribute( 'meta' )

		const postId = select( 'core/editor' ).getCurrentPostId()

		const libraryInfo = select( 'ramp' ).getLibraryInfo( postId )

		return {
			apiKey: zotero_api_key,
			groupId: zotero_group_id,
			groupUrl: zotero_group_url,
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

	const setGroupId = ( value ) => {
		dispatch( 'core/editor' ).editPost( {
			meta: {
				'zotero_group_id': value
			}
		} )
	}

	const setGroupUrl = ( value ) => {
		dispatch( 'core/editor' ).editPost( {
			meta: {
				'zotero_group_url': value
			}
		} )
	}

	const setApiKey = ( value ) => {
		dispatch( 'core/editor' ).editPost( {
			meta: {
				'zotero_api_key': value
			}
		} )
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
				label={ __( 'Group ID', 'ramp' ) }
				value={ groupId }
				onChange={ (value) => setGroupId( value ) }
			/>

			<TextControl
				label={ __( 'Group URL', 'ramp' ) }
				value={ groupUrl }
				onChange={ (value) => setGroupUrl( value ) }
			/>

			<TextControl
				label={ __( 'API Key', 'ramp' ) }
				value={ apiKey }
				onChange={ (value) => setApiKey( value ) }
			/>

			{nextIngest}

			<Button
				variant="primary"
				onClick={ triggerIngest }
			>Trigger Zotero sync</Button>
		</PluginDocumentSettingPanel>
	);
}
