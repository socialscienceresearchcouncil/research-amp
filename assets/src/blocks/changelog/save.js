import { InnerBlocks, useBlockProps } from '@wordpress/block-editor'

export default function save( { attributes } ) {
	const {
		headingText
	} = attributes

	const blockProps = useBlockProps.save()

	return (
		<div { ...blockProps }>
			<h2 className="has-h-5-font-size">{ headingText }</h2>
			<InnerBlocks.Content />
		</div>
	)
}
