import './editor.scss'

import { __, sprintf } from '@wordpress/i18n'

import classNames from 'classnames'

import { dispatch, select } from '@wordpress/data'
import { store } from '@wordpress/editor'

import { PluginDocumentSettingPanel } from '@wordpress/edit-post'

import { useEntityProp } from '@wordpress/core-data';

import {
	dateI18n,
	__experimentalGetSettings as getDateSettings,
} from '@wordpress/date';

import {
	InnerBlocks,
	RichText,
	useBlockProps
} from '@wordpress/block-editor'

import { parse, getSaveContent } from '@wordpress/blocks'

import { useSelect, useDispatch } from '@wordpress/data'

export default function edit( {
	clientId,
	context: { postType, postId },
	props,
	attributes,
	setAttributes
} ) {
	const {
		changelogText,
		headingText
	} = attributes

	const {
		changelogIsDirty
	} = useSelect(
		( select ) => {

			const originalPost = select( 'core/editor' ).getCurrentPost().content

			const block = select( 'core/block-editor' ).getBlock( clientId )
			const currentChangelogContent = getSaveContent(block.name, block.attributes,block.innerBlocks)

			return {
				changelogIsDirty: -1 === originalPost.indexOf( currentChangelogContent )
			};
		},
		[]
	);

	const blockProps = useBlockProps()

	const headingTextValue = headingText ?? __( 'Changelog', 'research-amp' )

	const { createWarningNotice, removeNotice } = useDispatch( 'core/notices' )

	if ( changelogIsDirty ) {
		removeNotice( 'ramp-changelog-lock' )

	} else {
		createWarningNotice( __( 'You have not added a Changelog entry', 'research-amp' ),
			{
				id: 'ramp-changelog-lock'
			}
		)
	}

	const { setChangelogIsDirty } = useDispatch( 'research-amp' )

	const handleChangelogContentChange = (changelogText ) => {
		setChangelogIsDirty( true )
		setAttributes( { changelogText } )
	}

	const dateSettings = getDateSettings();
	const [ siteFormat = dateSettings.formats.date ] = useEntityProp(
		'root',
		'site',
		'date_format'
	);

	const defaultDateText = dateI18n( siteFormat, Date.now() )

	const defaultBlocks = [
		[ 'ramp/changelog-entry', { 'dateText': defaultDateText, 'entryText': '<li>' + __( 'Initial publication', 'research-amp' ) + '</li>' } ]
	]

	return (
		<>
			<div { ...blockProps }>
				<RichText
					className="changelog-title"
					onChange={ (headingText) => setAttributes( { headingText } ) }
					tagName="h5"
					value={ headingTextValue }
				/>

				<InnerBlocks
					allowedBlocks={ [ 'ramp/changelog-entry' ] }
					orientation="vertical"
					renderAppender={ InnerBlocks.ButtonBlockAppender }
					template={ defaultBlocks }
				/>
			</div>
		</>
	)
}
