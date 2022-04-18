import { RichText, useBlockProps } from '@wordpress/block-editor'

export default function save( { attributes } ) {
	const {
		dateText,
		entryText
	} = attributes

	const blockProps = useBlockProps.save()

	return (
		<div { ...blockProps }>
			<h4 className="has-body-text-font-size">{ dateText }</h4>
			<RichText.Content
				tagName="ul"
				value={ entryText }
			/>
		</div>
	)
}
