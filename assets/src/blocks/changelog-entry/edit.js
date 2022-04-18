import './editor.scss'

import { __, sprintf } from '@wordpress/i18n'

import classNames from 'classnames'

import { dispatch, select } from '@wordpress/data'
import { store } from '@wordpress/editor'

import { PluginDocumentSettingPanel } from '@wordpress/edit-post'

import { Button } from '@wordpress/components'

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
		dateText,
		entryText
	} = attributes

	const blockProps = useBlockProps()

	return (
		<>
			<div { ...blockProps }>
				<RichText
					className="changelog-entry-date"
					onChange={ (dateText) => setAttributes( { dateText } ) }
					tagName="h6"
					value={ dateText }
					placeholder={ __( 'Date', 'ramp' ) }
				/>

				<RichText
					className="changelog-entry-content"
					onChange={ (entryText) => setAttributes( { entryText } ) }
					value={ entryText }
					tagName="ul"
					multiline="li"
				/>
			</div>
		</>
	)
}
