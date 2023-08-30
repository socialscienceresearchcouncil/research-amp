import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

export default function save( { attributes } ) {
	const { headingText } = attributes;

	const blockProps = useBlockProps.save();

	return (
		<div { ...blockProps }>
			<div className="changelog-header">
				<h2 className="has-h-5-font-size">{ headingText }</h2>
			</div>

			<InnerBlocks.Content />
		</div>
	);
}
