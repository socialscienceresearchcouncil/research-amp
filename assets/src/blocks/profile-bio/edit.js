import './editor.scss'

import { __ } from '@wordpress/i18n'
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor'
import { TextControl } from '@wordpress/components'

export default function edit( {
	attributes,
	setAttributes
} ) {
	const {
		content
	} = attributes

	const blockProps = useBlockProps()

	const template = [
		[
			'core/paragraph',
			{
				'placeholder': __( 'Enter the profile biography', 'research-amp' )
			}
		]
	]

	return (
		<div { ...blockProps }>
			<InnerBlocks
				template={ template }
			/>
		</div>
	)
}
