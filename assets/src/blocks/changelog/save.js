import { InnerBlocks, useBlockProps } from '@wordpress/block-editor'

export default function save( { attributes } ) {
	const {
		headingText
	} = attributes

	const blockProps = useBlockProps.save()

	return (
		<div { ...blockProps }>
			<h3 className="has-h-5-font-size">{ headingText }</h3>
			<InnerBlocks.Content />
		</div>
	)
}
