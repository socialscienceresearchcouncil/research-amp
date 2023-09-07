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
 * @param  root0
 * @param  root0.attributes
 * @param  root0.setAttributes
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function edit( { attributes } ) {
	const { editPost } = dispatch( 'core/editor' );

	const {
		apiKey,
		collectionMap,
		isConnected,
		isNew,
		libraryId,
		libraryInfo,
		postId,
		postTitle,
		researchTopics,
	} = useSelect( ( select ) => {
		const { zotero_api_key, zotero_library_id } = select( 'core/editor' ).getEditedPostAttribute( 'meta' );

		const collectionMap = select( 'core/editor' ).getEditedPostAttribute( 'collection_map' );

		const postTitle = select( 'core/editor' ).getEditedPostAttribute( 'title' );
		const postId = select( 'core/editor' ).getCurrentPostId();

		const isNew = select( 'core/editor' ).isEditedPostNew();

		const libraryInfo = select( 'research-amp' ).getLibraryInfo( postId );
		const researchTopics = select( 'research-amp' ).getResearchTopics();

		const isConnected =
			'undefined' !== typeof libraryInfo &&
			libraryInfo.hasOwnProperty( 'isConnected' )
				? libraryInfo.isConnected
				: null;

		return {
			apiKey: zotero_api_key,
			collectionMap,
			isConnected,
			isNew,
			libraryId: zotero_library_id,
			libraryInfo,
			postId,
			postTitle,
			researchTopics,
		};
	}, [] );

	const triggerIngest = () => {
		apiFetch( {
			path: addQueryArgs( 'research-amp/v1/zotero-library-info', {
				libraryId: postId,
				action: 'sync',
			} ),
			method: 'POST',
		} ).then( ( res ) => {} );
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

	const collectionList = libraryInfo?.collectionList || {};

	const sortedCollectionKeys = Object.keys( collectionList ).sort(
		( a, b ) => {
			const aName = collectionList[ a ].name;
			const bName = collectionList[ b ].name;

			if ( aName === bName ) {
				return 0;
			}

			return aName.localeCompare( bName ) > 0 ? 1 : -1;
		}
	);

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

	return (
		<>
			<div { ...blockProps }>
				<fieldset>
					<legend>
						{ __( 'Library Settings', 'research-amp' ) }
					</legend>

					{ ! isNew && false === isConnected && (
						<p className="connection-error">
							{ __( 'Could not connect to your Zotero library. Please check your Library ID and API key.', 'research-amp' ) }
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
													label={ __( 'Select a Research Topic', 'ramp' ) }
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

						{ sortedCollectionKeys.length === 0 && (
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
