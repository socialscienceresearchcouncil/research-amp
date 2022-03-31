import './editor.scss'

import { __, sprintf } from '@wordpress/i18n'
import { useBlockProps } from '@wordpress/block-editor'
import { TextControl } from '@wordpress/components'

export default function edit( {
	attributes,
	setAttributes
} ) {
	const {
		content
	} = attributes

	const blockProps = useBlockProps()

	return (
		<div { ...blockProps }>
			<TextControl
				hideLabelFromVision={ true }
				label={ __( 'Title and institution', 'ramp' ) }
				onChange={ ( content ) => setAttributes( { content } ) }
				placeholder={ __( 'Enter title and institution', 'ramp' ) }
				value={ content }
			/>
		</div>
	)
}
