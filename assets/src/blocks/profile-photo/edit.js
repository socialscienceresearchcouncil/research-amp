import './editor.scss';

import { __, sprintf } from '@wordpress/i18n';
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import { TextControl } from '@wordpress/components';

const blockTemplate = [
	[
		'core/post-featured-image',
		{
			height: '300px',
			width: '300px',
			scale: 'cover',
		},
	],
];

export default function edit( { attributes, setAttributes } ) {
	const { content } = attributes;

	const blockProps = useBlockProps();

	return (
		<div { ...blockProps }>
			<InnerBlocks template={ blockTemplate } templateLock="all" />
		</div>
	);
}
