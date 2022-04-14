import './editor.scss'

import { __, sprintf } from '@wordpress/i18n'

import classNames from 'classnames'

import { dispatch, select } from '@wordpress/data'
import { store } from '@wordpress/editor'

import { PluginDocumentSettingPanel } from '@wordpress/edit-post'

import { CheckboxControl } from '@wordpress/components'

import {
	RichText,
	useBlockProps
} from '@wordpress/block-editor'

import { useSelect, useDispatch } from '@wordpress/data'

export default function edit( {
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
		changelogDirtyBypass,
		changelogIsDirty
	} = useSelect(
		( select ) => {
			const changelogDirtyBypass = select( 'ramp' ).getChangelogDirtyBypass()
			const changelogIsDirty = select( 'ramp' ).getChangelogIsDirty()

			return {
				changelogDirtyBypass,
				changelogIsDirty
			};
		},
		[]
	);

	const blockProps = useBlockProps()

	const headingTextValue = headingText ?? __( 'Changelog', 'ramp' )

	const { lockPostSaving, unlockPostSaving } = dispatch( 'core/editor' )
	const { createWarningNotice, removeNotice } = dispatch( 'core/notices' )

	if ( changelogIsDirty || changelogDirtyBypass ) {
		unlockPostSaving( 'ramp-changelog-lock' )
		removeNotice( 'ramp-changelog-lock' )

	} else {
		lockPostSaving( 'ramp-changelog-lock' )
		createWarningNotice( 'You have not added a Changelog entry',
			{
				id: 'ramp-changelog-lock',
				onDismiss: () => {
					unlockPostSaving( 'ramp-changelog-lock' )
				}
			}
		)
	}

	const { setChangelogDirtyBypass, setChangelogIsDirty } = dispatch( 'ramp' )

	const handleChangelogContentChange = (changelogText ) => {
		setChangelogIsDirty( true )
		setAttributes( { changelogText } )
	}

	return (
		<>
			<div { ...blockProps }>
				<RichText
					className="changelog-title"
					onChange={ (headingText) => setAttributes( { headingText } ) }
					tagName="h3"
					value={ headingTextValue }
				/>

				<RichText
					className="changelog-content"
					onChange={ handleChangelogContentChange }
					value={ changelogText }
					placeholder={ __( 'Enter changelog text', 'ramp' ) }
				/>
			</div>
		</>
	)
}
