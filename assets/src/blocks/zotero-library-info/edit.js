/* eslint camelcase: ["error", { "allow": ["collection_map", "zotero_api_key", "zotero_library_id"] }] */

import { __, sprintf } from '@wordpress/i18n';

import { unescapeString } from '../../components/ReorderableFlatTermSelector/utils';

import { useBlockProps } from '@wordpress/block-editor';

import {
	Button,
	SelectControl,
	TextControl,
} from '@wordpress/components';

import { dispatch, useSelect } from '@wordpress/data';
import { addQueryArgs } from '@wordpress/url';

import apiFetch from '@wordpress/api-fetch';

/**
 * Editor styles.
 */
import './editor.scss';

/**
 * Edit function.
 *
 * @param {Object} root0
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function Edit( {} ) {
	const { editPost } = dispatch( 'core/editor' );

	const {
		apiKey,
		collectionMap,
		isNew,
		libraryId,
		libraryInfo,
		postId,
		postTitle,
		researchTopics,
	} = useSelect( ( select ) => {
		const { zotero_api_key, zotero_library_id } = select( 'core/editor' ).getEditedPostAttribute( 'meta' );

		const thisPostId = select( 'core/editor' ).getCurrentPostId();

		return {
			apiKey: zotero_api_key,
			collectionMap: select( 'core/editor' ).getEditedPostAttribute( 'collection_map' ),
			isNew: select( 'core/editor' ).isEditedPostNew(),
			libraryId: zotero_library_id,
			libraryInfo: select( 'research-amp' ).getLibraryInfo( thisPostId ),
			postId: thisPostId,
			postTitle: select( 'core/editor' ).getEditedPostAttribute( 'title' ),
			researchTopics: select( 'research-amp' ).getResearchTopics(),
		};
	}, [] );

	const isConnected =
		'undefined' !== typeof libraryInfo &&
		libraryInfo.hasOwnProperty( 'isConnected' )
			? libraryInfo.isConnected
			: null;

	const triggerIngest = () => {
		apiFetch( {
			path: addQueryArgs( 'research-amp/v1/zotero-library-info', {
				libraryId: postId,
				action: 'sync',
			} ),
			method: 'POST',
		} ).then( () => {} );
	};

	const editPostMeta = ( newMeta ) => {
		editPost( { meta: newMeta } );
	};

	const editPostTitle = ( title ) => {
		editPost( { title } );
	};

	const lastIngest = libraryInfo?.lastIngest;
	const lastIngestRelative = libraryInfo?.lastIngestRelative;
	const nextIngest = libraryInfo?.nextIngest;
	const nextIngestRelative = libraryInfo?.nextIngestRelative;

	const collectionList = libraryInfo && libraryInfo.hasOwnProperty( 'collectionList' ) ? libraryInfo.collectionList : {};

	const sortedCollectionKeys =
		null !== collectionList
			? Object.keys( collectionList ).sort(
				( a, b ) => {
					const aName = collectionList[ a ].name;
					const bName = collectionList[ b ].name;

					if ( aName === bName ) {
						return 0;
					}

					return aName.localeCompare( bName ) > 0 ? 1 : -1;
				}
			)
			: [];

	const customClassNames = [ 'ramp-zotero-library-info' ];
	const blockProps = useBlockProps( { className: customClassNames } );

	const researchTopicsOptions = researchTopics.map( ( topic ) => {
		return {
			label: unescapeString( topic.title.rendered ),
			value: topic.id,
		};
	} );

	const allOptions = [
		...[ { label: '-', value: 0 } ],
		...researchTopicsOptions,
	];

	const setCollectionMap = ( collectionKey, researchTopicId ) => {
		const newMap = Object.assign( {}, collectionMap, {
			[ collectionKey ]: parseInt( researchTopicId ),
		} );

		editPost( {
			collection_map: newMap,
		} );
	};

	const getConnectionStatusMessage = () => {
		switch ( isConnected ) {
			case 'invalid_credentials' :
				return __( 'Your Zotero credentials are invalid. Please check your Library ID and API key.', 'research-amp' );

			case 'no_read_access_to_user_library' :
				return __( 'Your Zotero API key does not have read access to your library. Please check your API key, and ensure that it is configured properly..', 'research-amp' );

			case 'no_read_access_to_group_library' :
				return __( 'Your Zotero API key does not have read access to the specified group library. Please check your API key, and ensure that is configured properly.', 'research-amp' );
		}
	}

	return (
		<>
			<div { ...blockProps }>
				<fieldset>
					<legend>
						{ __( 'Library Settings', 'research-amp' ) }
					</legend>

					{ ! isNew && 'valid' !== isConnected && (
						<p className="connection-error">
							{ getConnectionStatusMessage() }
						</p>
					) }

					<TextControl
						label={ __( 'Library Name', 'research-amp' ) }
						value={ postTitle }
						onChange={ ( newPostTitle ) => {
							editPostTitle( newPostTitle );
						} }
						help={ __( 'Displayed on individual Citation pages, to indicate the source library for that item.', 'research-amp' ) }
					/>

					<TextControl
						label={ __( 'Library ID', 'research-amp' ) }
						value={ libraryId }
						onChange={ ( value ) => {
							editPostMeta( { zotero_library_id: value } );
						} }
					/>

					<TextControl
						label={ __( 'API Key', 'research-amp' ) }
						type="password"
						value={ apiKey }
						onChange={ ( value ) => {
							editPostMeta( { zotero_api_key: value } );
						} }
					/>
				</fieldset>

				{ ! isNew && (
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

						<p>
							{ __( 'You may trigger an immediate sync by clicking the button below. (New sync timestamps may not appear for a few minutes.)', 'research-amp' ) }
						</p>

						<Button variant="primary" onClick={ triggerIngest }>
							{ __( 'Trigger Zotero sync', 'research-amp' ) }
						</Button>
					</fieldset>
				) }

				{ ! isNew && (
					<fieldset>
						<legend>
							{ __(
								'Collections & Research Topics',
								'research-amp'
							) }
						</legend>

						<p>
							{ __( 'When importing from the Zotero library, a new Citation is associated with Research Topics according to the Collections in which the item appears in the library. Use this interface to associate Zotero Collections with Research Topics.', 'research-amp' ) }
						</p>

						{ sortedCollectionKeys.length > 0 && (
							<ul className="collection-mapper">
								{ sortedCollectionKeys.map(
									( collectionKey ) => (
										<li
											key={ 'collection-' + collectionKey }
											className="collection-row"
										>
											<div className="collection-map-name">
												<a
													href={
														collectionList[
															collectionKey
														].url
													}
													target="_blank"
													rel="noreferrer"
												>
													{ collectionList[ collectionKey ].name }
												</a>
											</div>

											<div className="collection-map-selector">
												<SelectControl
													hideLabelFromVision={ true }
													label={ __( 'Select a Research Topic', 'research-amp' ) }
													onChange={ ( selected ) => {
														setCollectionMap(
															collectionKey,
															selected
														);
													} }
													options={ allOptions }
													value={ collectionMap.hasOwnProperty( collectionKey) !== -1 ? collectionMap[ collectionKey ] : 0 }
													__nextHasNoMarginBottom={ true }
												/>
											</div>
										</li>
									)
								) }
							</ul>
						) }

						{ sortedCollectionKeys.length ===  0 && null === collectionList && (
							<p>
								{ __( 'We could not connect to your Zotero library to fetch Collections. Please see above for error message.', 'research-amp' ) }
							</p>
						) }

						{ sortedCollectionKeys.length === 0 && null !== collectionList && (
							<p>
								{ __( 'No Collections have been found for this Zotero Library. If you think you should be seeing Collections here, try refreshing the page.', 'research-amp' ) }
							</p>
						) }
					</fieldset>
				) }
			</div>
		</>
	);
}
