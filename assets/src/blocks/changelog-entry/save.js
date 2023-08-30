import { RichText, useBlockProps } from '@wordpress/block-editor';

export default function save( { attributes } ) {
	const { dateText, entryText } = attributes;

	const blockProps = useBlockProps.save();

	return (
		<div { ...blockProps }>
			<p className="changelog-entry-date">{ dateText }</p>
			<RichText.Content tagName="ul" value={ entryText } />
		</div>
	);
}
