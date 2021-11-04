import { TextControl } from '@wordpress/components';
import { dispatch, select } from '@wordpress/data';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { Component } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';
import { useState } from '@wordpress/element';
import { store as coreStore } from '@wordpress/core-data';

export default function ZoteroLibraryInfo( {
	attributes,
	setAttributes
} ) {
	const postType = select( 'core/editor' ).getCurrentPostType();
	if ( 'ssrc_zotero_library' !== postType ) {
		return null;
	}

	const { apiKey, groupId, groupUrl } = useSelect( (select ) => {
		const {
			zotero_api_key,
			zotero_group_id,
			zotero_group_url
		} = select( 'core/editor' ).getEditedPostAttribute( 'meta' )

		return {
			apiKey: zotero_api_key,
			groupId: zotero_group_id,
			groupUrl: zotero_group_url
		}
	}, [] )

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
		</PluginDocumentSettingPanel>
	);
}
