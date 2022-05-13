import './editor.scss'

import { __, sprintf } from '@wordpress/i18n'

import classNames from 'classnames'

import {
	RichText,
	useBlockProps
} from '@wordpress/block-editor'

import { useSelect } from '@wordpress/data'

import { store as coreStore } from '@wordpress/core-data'

export default function edit( {
	context: { postType, postId },
	attributes,
	setAttributes
} ) {
	const {
		headingText,
		helpText
	} = attributes

	const blockProps = useBlockProps({
		className: [ 'sidebar-section' ]
	})

	const { citation } = useSelect(
		( select ) => {
			const { getEditedEntityRecord } = select( coreStore )

			const _citation = getEditedEntityRecord(
				'postType',
				postType,
				postId
			)?.formatted_citation;

			return {
				citation: _citation ?? __( 'Author, Sample. "Example of a citation." March 3, 2005. Your Site. https://example.com/sample-citation', 'research-amp' )
			};
		},
		[ postType, postId ]
	);

	const headingTextValue = headingText ?? __( 'Cite This', 'research-amp' )
	const helpTextValue = helpText ?? __( 'Copy and paste the text below to cite this item.', 'research-amp' )

	return (
		<>
			<div { ...blockProps }>
				<>
					<RichText
						className="sidebar-section-title"
						onChange={ (headingText) => setAttributes( { headingText } ) }
						tagName="h3"
						value={ headingTextValue }
					/>

					<RichText
						className="cite-this-help-text"
						onChange={ (helpText) => setAttributes( { helpText } ) }
						tagName="p"
						value={ helpTextValue }
					/>

					<div className="cite-this-citation">
						{ citation }
					</div>
				</>
			</div>
		</>
	)
}
